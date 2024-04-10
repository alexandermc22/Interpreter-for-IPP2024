<?php

namespace IPP\Student\Exception;
use IPP\Core\Exception\IPPException;
use Throwable;

class InvalidOperandError extends IPPException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct("Invalid operand.", 57, $previous);
    }
}