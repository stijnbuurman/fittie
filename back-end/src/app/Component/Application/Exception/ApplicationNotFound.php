<?php

namespace Fittie\Component\Application\Exception;

use Exception;
use Throwable;

class ApplicationNotFound extends Exception
{
    public function __construct($applicationName = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('Application could not be found: ' . $applicationName, $code, $previous);
    }
}
