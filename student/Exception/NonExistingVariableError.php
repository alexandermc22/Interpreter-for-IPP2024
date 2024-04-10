<?php

namespace IPP\Student\Exception;
use IPP\Core\Exception\IPPException;
use Throwable;

class NonExistingVariableError extends IPPException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct("Variable does not exist.", 54, $previous);
    }
}