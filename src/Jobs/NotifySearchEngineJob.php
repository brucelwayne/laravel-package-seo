<?php

namespace Brucelwayne\SEO\Jobs;

use Brucelwayne\SEO\Models\SeoIndexedModel;
use Google\Client;
use Google\Service\Indexing;

class NotifySearchEngineJob extends AbstractSEOJob
{
    protected const NAME = 'notify-search-engine-job';
    public $queue = 'seo';
    protected $model;
    protected string $locale;
    protected string $url;
    protected string $searchEngine;
    protected string $modelType;
    protected int $modelId;

    /**
     * Create a new job instance.
     *
     * @param $model
     * @param string $locale
     * @param string $url
     * @param string $searchEngine
     *
     */
    public function __construct($model, string $locale, string $url, string $searchEngine = 'google')
    {
        parent::__construct($model, $locale, $url, $searchEngine, '');

    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $this->runTransaction(function () {
            $jobable = $this->getJobable($this->model);
            if (!empty($jobable)) {
                $jobable->update(['failed_job_id' => null]);
            }
            $response = null;
            if ($this->searchEngine === 'google') {
                $response = $this->notifyGoogle();
            }
            // 记录到数据库
            $this->recordIndexing($response);

            $this->deleteJob();
        });
    }

    /**
     * Notify Google to index the URL.
     *
     * @return array|null
     * @throws \Exception
     */
    protected function notifyGoogle(): ?array
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/index-service-account.json'));
        $client->addScope('https://www.googleapis.com/auth/indexing');

        $service = new Indexing($client);

        $body = new Indexing\UrlNotification();
        $body->setType('URL_UPDATED');
        $body->setUrl($this->url);

        // 调用 API 提交索引
        $response = $service->urlNotifications->publish($body);

        return (array)$response;
    }

    /**
     * Record the indexing result into the database.
     *
     * @param array|null $response
     * @return void
     */
    protected function recordIndexing(?array $response): void
    {
        SeoIndexedModel::updateOrCreate(
            [
                'model_type' => $this->model->getMorphClass(),
                'model_id' => $this->model->getKey(),
            ],
            [
                'locale' => $this->locale,
                'url' => $this->url,
                'google_indexed_at' => now(),
                'response' => json_encode($response),
                'payload' => [
                    'search_engine' => $this->searchEngine,
                ],
            ]
        );
    }
}

