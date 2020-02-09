<?php

namespace Fittie\Component\Application\Event;

use Fittie\Common\Instrumentation;
use Fittie\Component\Application\Repository\ApplicationConnectionRepositoryInterface;
use Illuminate\Events\Dispatcher;

class ApplicationEventSubscriber
{
    private Instrumentation $instrumentation;

    public function __construct(Instrumentation $instrumentation)
    {
        $this->instrumentation = $instrumentation;
    }

    public function onApplicationConnectionCreated(ApplicationConnectionCreated $event)
    {
        $this->instrumentation->eventFired($event);

        /** @var ApplicationConnectionRepositoryInterface $applicationRepository */
        $applicationRepository = app()->make(ApplicationConnectionRepositoryInterface::class);
        $applicationRepository->save($event->getApplicationConnection());
    }

    public function onApplicationConnectionUpdated(ApplicationConnectionUpdated $event)
    {
        $this->instrumentation->eventFired($event);

        /** @var ApplicationConnectionRepositoryInterface $applicationRepository */
        $applicationRepository = app()->make(ApplicationConnectionRepositoryInterface::class);
        $applicationRepository->save($event->getApplicationConnection());
    }


    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            ApplicationConnectionCreated::class,
            ApplicationEventSubscriber::class . '@onApplicationConnectionCreated'
        );

        $events->listen(
            ApplicationConnectionUpdated::class,
            ApplicationEventSubscriber::class . '@onApplicationConnectionUpdated'
        );
    }
}
