<?php
namespace Fittie\Component\Application\Entity;

use Fittie\Component\Analytics\Metric\MetricType;
use Fittie\Component\Application\Client\ApplicationClientInterface;
use Fittie\Component\ApplicationAuth\AuthFlow;
use Fittie\Component\ApplicationAuth\Credentials;

interface ApplicationInterface
{
    /**
     * @returns MetricType[]
     */
    public function availableMetricTypes(): array;

    public function toArray(): array;

    public function getAuthFlow(): AuthFlow;

    public function getName(): string;

    public function getSlug(): string;

    public function makeClient(Credentials $credentials = null): ApplicationClientInterface;

    public function getScopes(): array;
}
