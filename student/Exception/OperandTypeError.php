<?php

namespace IPP\Student\Exception;
use IPP\Core\Exception\IPPException;
use Throwable;

class OperandTypeError extends IPPException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct("Operand type error.", 53, $previous);
    }
}