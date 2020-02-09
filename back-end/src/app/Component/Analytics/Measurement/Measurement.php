<?php


namespace Fittie\Component\Analytics;


use \DateTime;

class Measurement
{
    protected DateTime $start;
    protected $value;

    public function __construct(DateTime $start, $value)
    {
        $this->start = $start;
        $this->value = $value;
    }

    public function getStart(): DateTime
    {
        return $this->start;
    }

    public function getValue()
    {
        return $this->value;
    }
}
