<?php
namespace Fittie\Component\Application\Client;

use Fittie\Component\Analytics\DataSet;
use Fittie\Component\Analytics\MeasurementsRequest;

interface ApplicationClientInterface
{
    public function getMeasurements(MeasurementsRequest $request): DataSet;
}
