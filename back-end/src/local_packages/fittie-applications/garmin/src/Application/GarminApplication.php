<?php

namespace FittieApplications\Garmin\Application;

use Fittie\Component\Analytics\Metric\MetricType;
use Fittie\Component\ApplicationAuth\AuthFlow;
use Fittie\Component\ApplicationAuth\Credentials;
use Fittie\Component\ApplicationAuth\UserPassword\AuthFlow\UserPasswordAuthFlow;
use Fittie\Component\ApplicationAuth\UserPassword\Credentials\UserPasswordCredentials;
use Fittie\Component\Application\Client\ApplicationClientInterface;
use Fittie\Component\Application\Entity\ApplicationInterface;
use FittieApplications\Garmin\Auth\GarminUserPasswordClient;

class GarminApplication implements ApplicationInterface
{
    public function getName(): string
    {
        return 'Garmin';
    }

    public function getSlug(): string
    {
        return 'garmin';
    }

    public function makeClient(Credentials $credentials = null): ApplicationClientInterface
    {
        if (!($credentials instanceof UserPasswordCredentials)) {
            throw new \Exception('Invalid credentials');
        }

        return GarminUserPasswordClient::makeClient($credentials);
    }

    public function getScopes(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function availableMetricTypes(): array
    {
        return [
            MetricType::steps(),
            MetricType::restingHeartRate(),
        ];
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
        ];
    }

    public function getAuthFlow(): AuthFlow
    {
        return new UserPasswordAuthFlow();
    }
}
