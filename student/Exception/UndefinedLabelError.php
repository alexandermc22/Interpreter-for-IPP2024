<?php

namespace IPP\Student\Exception;
use IPP\Core\Exception\IPPException;
use Throwable;

class UndefinedLabelError extends IPPException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct("Undefined label used.", 52, $previous);
    }
}