<?php

namespace Fittie\Component\Application\Event;

use Fittie\Common\Event;
use Fittie\Component\Application\Entity\ApplicationConnection;

class ApplicationConnectionCreated extends Event
{
    private ApplicationConnection $applicationConnection;

    public function __construct(ApplicationConnection $applicationAuth)
    {
        $this->applicationConnection = $applicationAuth;
    }

    public function getApplicationConnection(): ApplicationConnection
    {
        return $this->applicationConnection;
    }

    public function getData(): array
    {
        return $this->applicationConnection->toArray();
    }

    public function getName(): string
    {
        return 'application-auth.created';
    }
}
