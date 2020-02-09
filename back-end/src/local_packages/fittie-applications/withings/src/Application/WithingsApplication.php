<?php

namespace FittieApplications\Withings\Application;

use Exception;
use Fittie\Component\Analytics\Metric\MetricType;
use Fittie\Component\ApplicationAuth\AuthFlow;
use Fittie\Component\ApplicationAuth\Credentials;
use Fittie\Component\ApplicationAuth\OAuth2\AuthFlow\OAuth2AuthFlow;
use Fittie\Component\Application\Client\ApplicationClientInterface;
use Fittie\Component\Application\Entity\ApplicationInterface;
use Fittie\Component\ApplicationAuth\OAuth2\Credentials\OAuth2Credentials;
use FittieApplications\Withings\OAuth\Client\WithingsOAuthClient;

class WithingsApplication implements ApplicationInterface
{
    public function getName(): string
    {
        return 'Withings';
    }

    public function getSlug(): string
    {
        return 'withings';
    }

    public function makeClient(Credentials $credentials = null): ApplicationClientInterface
    {
        if ($credentials !== null && !($credentials instanceof OAuth2Credentials)) {
            throw new Exception('Invalid credentials');
        }

        return WithingsOAuthClient::makeClient($credentials);
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
            MetricType::weight(),
            MetricType::fatFreeMass(),
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
        return new OAuth2AuthFlow();
    }
}
