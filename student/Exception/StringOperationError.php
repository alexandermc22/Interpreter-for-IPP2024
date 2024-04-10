<?php

namespace IPP\Student\Exception;
use IPP\Core\Exception\IPPException;
use Throwable;
class StringOperationError extends IPPException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct("String operation error.", 58, $previous);
    }
}