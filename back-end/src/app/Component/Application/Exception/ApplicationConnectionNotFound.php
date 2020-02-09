<?php

namespace Fittie\Component\Application\Exception;

use Exception;
use Fittie\Component\Application\Entity\ApplicationConnectionID;
use Throwable;

class ApplicationConnectionNotFound extends Exception
{
    public function __construct(ApplicationConnectionID $applicationConnectionID, $code = 0, Throwable $previous = null)
    {
        parent::__construct('Application connection could not be found: ' . $applicationConnectionID, $code, $previous);
    }
}
