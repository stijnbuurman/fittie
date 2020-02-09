<?php

namespace Fittie\Component\Analytics;

class DataSet
{
    private Metric $metric;
    private array $measurements;

    public function __construct(Metric $metric, array $measurements = [])
    {
        $this->metric = $metric;
        $this->measurements = $measurements;
    }

    public function addMeasurement(Measurement $measurement)
    {
        $this->measurements[] = $measurement;
    }

    public function getMetric(): Metric
    {
        return $this->metric;
    }

    public function getMeasurements(): array
    {
        return $this->measurements;
    }

    public function toArray()
    {
        return array_map(
            fn(Measurement $measurement) => ['start' => $measurement->getStart()->getTimestamp(), 'value' => $measurement->getValue()],
            $this->measurements
        );
    }
}
