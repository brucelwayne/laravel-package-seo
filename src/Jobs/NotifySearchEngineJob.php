<?php

namespace Brucelwayne\SEO\Jobs;

use Brucelwayne\SEO\Models\SeoIndexedModel;
use Google\Client;
use Google\Service\Indexing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifySearchEngineJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $url;
    protected string $searchEngine;
    protected string $modelType;
    protected int $modelId;

    /**
     * Create a new job instance.
     *
     * @param string $url
     * @param string $searchEngine
     * @param string $modelType
     * @param int $modelId
     */
    public function __construct(string $url, string $searchEngine, string $modelType, int $modelId)
    {
        $this->url = $url;
        $this->searchEngine = strtolower($searchEngine);
        $this->modelType = $modelType;
        $this->modelId = $modelId;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $response = null;

        if ($this->searchEngine === 'google') {
            $response = $this->notifyGoogle();
        }

        // 记录到数据库
        $this->recordIndexing($response);
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
                'model_type' => $this->modelType,
                'model_id' => $this->modelId,
            ],
            [
                'url' =>$this->url,
                'google_indexed_at' => now(),
                'response' => json_encode($response),
                'payload' => [
                    'url' => $this->url,
                    'search_engine' => $this->searchEngine,
                ],
            ]
        );
    }
}

