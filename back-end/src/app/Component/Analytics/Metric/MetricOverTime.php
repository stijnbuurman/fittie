<?php


namespace Fittie\Component\Analytics;


class MetricOverTime extends Metric
{
    private int $timeBucketSeconds;

    public function __construct(string $name, string $unit, int $timeBucketSizeSeconds)
    {
        parent::__construct($name, $unit);
        $this->timeBucketSeconds = $timeBucketSizeSeconds;
    }

    public function getTimeBucketSeconds()
    {
        return $this->timeBucketSeconds;
    }
}
