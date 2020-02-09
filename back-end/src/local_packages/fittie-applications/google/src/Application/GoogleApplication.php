<?php

namespace FittieApplications\Google\Application;

use Exception;
use Fittie\Component\Analytics\Metric\MetricType;
use Fittie\Component\ApplicationAuth\AuthFlow;
use Fittie\Component\ApplicationAuth\Credentials;
use Fittie\Component\ApplicationAuth\OAuth2\AuthFlow\OAuth2AuthFlow;
use Fittie\Component\Application\Client\ApplicationClientInterface;
use Fittie\Component\Application\Entity\ApplicationInterface;
use Fittie\Component\ApplicationAuth\OAuth2\Credentials\OAuth2Credentials;
use Fittie\Component\ApplicationAuth\OAuth2\ValueObject\Scope;
use FittieApplications\Google\OAuth\Client\GoogleOAuthClient;
use Google_Service_Fitness;
use Google_Service_Oauth2;

class GoogleApplication implements ApplicationInterface
{
    public function getName(): string
    {
        return 'Google';
    }

    public function getSlug(): string
    {
        return 'google';
    }

    public function makeClient(Credentials $credentials = null): ApplicationClientInterface
    {
        if ($credentials !== null && !($credentials instanceof OAuth2Credentials)) {
            throw new Exception('Invalid credentials');
        }

        return GoogleOAuthClient::makeClient($credentials);
    }

    public function getScopes(): array
    {
        return [
            new Scope('User Info', Google_Service_Oauth2::USERINFO_PROFILE),
            new Scope('Fitness Body', Google_Service_Fitness::FITNESS_BODY_READ),
            new Scope('Fitness Location', Google_Service_Fitness::FITNESS_LOCATION_READ),
            new Scope('Fitness Activities', Google_Service_Fitness::FITNESS_ACTIVITY_READ),
            new Scope('Blood Glucose', Google_Service_Fitness::FITNESS_BLOOD_GLUCOSE_READ),
        ];
    }

    /**
     * @inheritDoc
     */
    public function availableMetricTypes(): array
    {
        return [
            MetricType::steps(),
            MetricType::weight(),
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
