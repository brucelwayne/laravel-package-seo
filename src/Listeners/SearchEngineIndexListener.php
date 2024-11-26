<?php

namespace Brucelwayne\SEO\Listeners;

use Brucelwayne\SEO\Events\SearchEngineIndexEvent;
use Brucelwayne\SEO\Jobs\NotifySearchEngineJob;
use Mallria\Core\Models\BaseMysqlModel;

class SearchEngineIndexListener
{

    public function handle(SearchEngineIndexEvent $event)
    {
        /**
         * @var BaseMysqlModel $model
         */
        $model = $event->model;
        $searchEngine = $event->searchEngine;
        $url = $event->url;

        if ($url) {
            // 将任务派发给队列
            NotifySearchEngineJob::dispatch($url, $searchEngine, $model->getMorphClass(), $model->getKey())->onQueue('seo');
        }
    }

}
