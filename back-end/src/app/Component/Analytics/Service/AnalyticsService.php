<?php

namespace Fittie\Component\Analytics\Service;

use Fittie\Component\Analytics\Exception\InvalidMetricType;
use Fittie\Component\Analytics\MeasurementsRequest;
use Fittie\Component\Application\Entity\ApplicationConnection;

class AnalyticsService
{
    /**
     * @param ApplicationConnection $applicationConnection
     * @param MeasurementsRequest $request
     *
     * @return \Fittie\Component\Analytics\DataSet
     *@throws InvalidMetricType
     *
     */
    public function getMeasurements(ApplicationConnection $applicationConnection, MeasurementsRequest $request)
    {
        $authFlow = $applicationConnection->getApplication()->getAuthFlow();
        $client = $authFlow->getAuthenticatedClient($applicationConnection);

        return $client->getMeasurements($request);
    }
}
