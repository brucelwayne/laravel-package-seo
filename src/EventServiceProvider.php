<?php

namespace Brucelwayne\SEO;


use Brucelwayne\SEO\Events\NewSeoPostForwardedEvent;
use Brucelwayne\SEO\Listeners\NewSeoPostForwardedListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        NewSeoPostForwardedEvent::class => [
            NewSeoPostForwardedListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}