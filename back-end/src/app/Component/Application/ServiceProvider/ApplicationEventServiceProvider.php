<?php

namespace Fittie\Component\Application\ServiceProvider;

use Fittie\Providers\EventServiceProvider;
use Fittie\Component\Application\Event\ApplicationEventSubscriber;

class ApplicationEventServiceProvider extends EventServiceProvider
{
    protected $subscribe = [
        ApplicationEventSubscriber::class,
    ];
}
