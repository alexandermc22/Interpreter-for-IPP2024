<?php

namespace IPP\Student\Exception;
use IPP\Core\Exception\IPPException;
use Throwable;
class MissingValueError extends IPPException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct("Missing value.", 56, $previous);
    }
}