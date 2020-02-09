<?php


namespace Fittie\Component\Application\Registrar;

use Fittie\Component\Application\Entity\ApplicationInterface;
use Fittie\Component\Application\Exception\ApplicationNotFound;
use Illuminate\Support\Arr;

class ApplicationRegistrar
{
    private array $applications = [];

    public function add(ApplicationInterface $application)
    {
        $this->applications[$application->getSlug()] = $application;

        return $this;
    }

    public function get(string $applicationSlug): ApplicationInterface
    {
        $application = Arr::get($this->applications, $applicationSlug, null);

        if (!$application) {
            throw new ApplicationNotFound($applicationSlug);
        }

        return $application;
    }

    public function all()
    {
        return $this->applications;
    }
}
