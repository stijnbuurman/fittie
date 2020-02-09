<?php

namespace Tests\Feature\Component\Analytics\Controller;

use Fittie\Component\Analytics\DataSet;
use Fittie\Component\Analytics\MeasurementsRequest;
use Fittie\Component\Analytics\Service\AnalyticsService;
use Fittie\Component\Application\Client\ApplicationClientInterface;
use Fittie\Component\Application\Entity\ApplicationConnection;
use Fittie\Component\Application\Entity\ApplicationInterface;
use Fittie\Component\ApplicationAuth\AuthFlow;
use Fittie\Component\ApplicationAuth\Credentials;
use Tests\TestCase;

class AnalyticsServiceTest extends TestCase
{
    public function testGetDataset()
    {
        // Setup
        $applicationClient = $this->mock(ApplicationClientInterface::class);
        $application = $this->mockApplication(null, $applicationClient);
        $applicationConnection = $this->mockApplicationConnection($application);

        $measurementsRequest = $this->mock(MeasurementsRequest::class);

        $datasetMock = $this->mock(DataSet::class);
        $applicationClient->shouldReceive('getMeasurements')->andReturn($datasetMock);

        // Run
        $service = new AnalyticsService();
        $dataset = $service->getMeasurements($applicationConnection, $measurementsRequest);

        // Assert
        $this->assertEquals($datasetMock, $dataset);
    }
}
