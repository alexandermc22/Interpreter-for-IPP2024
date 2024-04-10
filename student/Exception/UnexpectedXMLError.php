<?php

namespace IPP\Student\Exception;

use IPP\Core\Exception\IPPException;
use Throwable;

class UnexpectedXMLError extends IPPException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct("Unexpected XML structure.", 32, $previous);
    }
}