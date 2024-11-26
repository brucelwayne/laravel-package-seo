<?php

namespace Brucelwayne\SEO\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SearchEngineIndexEvent
{
    use Dispatchable, SerializesModels;

    /**
     * The model being indexed.
     *
     * @var mixed
     */
    public $model;

    public $url;

    /**
     * The search engine being notified.
     *
     * @var string
     */
    public $searchEngine;

    /**
     * Create a new event instance.
     *
     * @param mixed $model The model associated with the indexing.
     * @param string $searchEngine The name of the search engine (e.g., 'Google').
     */
    public function __construct($model, string $url, string $searchEngine = 'google')
    {
        $this->model = $model;
        $this->url = $url;
        $this->searchEngine = $searchEngine;
    }
}
