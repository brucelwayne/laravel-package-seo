<?php

namespace Brucelwayne\SEO\Listeners;

use Brucelwayne\SEO\Events\SearchEngineIndexEvent;
use Brucelwayne\SEO\Jobs\AbstractSEOJob;
use Brucelwayne\SEO\Jobs\NotifySearchEngineJob;
use Illuminate\Bus\Dispatcher;
use Mallria\Core\Models\BaseMysqlModel;
use Mallria\Core\Models\PageModel;
use Mallria\Shop\Models\TransPostModel;
use Mallria\Shop\Models\TransProductModel;

class SearchEngineIndexListener
{

    public function handle(SearchEngineIndexEvent $event)
    {
        /**
         * @var BaseMysqlModel|PageModel|TransPostModel|TransProductModel $model
         */
        $model = $event->model;
        $locale = $event->locale;
        $searchEngine = $event->searchEngine;
        $url = $event->url;

        if ($url) {
            $jobable = $model->job()->where('job_name', NotifySearchEngineJob::getName())->first();
            if (empty($jobable)) {
                $job = (new NotifySearchEngineJob($model, $locale, $url, $searchEngine, ''))
                    ->onQueue('ai')
                    ->delay(10);
                $jobId = $this->dispatchJob($job);
                $model->jobs()->create([
                    'job_name' => NotifySearchEngineJob::getName(),
                    'job_id' => $jobId,
                ]);
            }
        }
    }

    private function dispatchJob(AbstractSEOJob $job): string
    {
        return app(Dispatcher::class)->dispatch($job);
    }
}
