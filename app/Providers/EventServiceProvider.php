<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\QueueLogEvent' => [
            'App\Listeners\QueueLogEventListener',
        ],
        'App\Events\QueryLogEvent' => [
            'App\Listeners\QueryLogEventListener',
        ],
        'App\Events\GitPushEvent' => [
            'App\Listeners\GitPushEventListener',
        ],
    ];
}
