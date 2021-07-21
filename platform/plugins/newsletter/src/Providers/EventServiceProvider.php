<?php

namespace Botble\Newsletter\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Botble\Newsletter\Events\SubscribeNewsletterEvent;
use Botble\Newsletter\Listeners\SubscribeNewsletterListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        SubscribeNewsletterEvent::class => [
            SubscribeNewsletterListener::class,
        ],
    ];
}
