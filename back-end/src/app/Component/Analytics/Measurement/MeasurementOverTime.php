<?php

namespace Fittie\Component\Analytics;

use DateTime;

class MeasurementOverTime extends Measurement
{
    protected DateTime $end;

    public function __construct(DateTime $start, DateTime $end, $value)
    {
        parent::__construct($start, $value);
        $this->end = $end;
    }

    public function getEnd(): DateTime
    {
        return $this->end;
    }
}
