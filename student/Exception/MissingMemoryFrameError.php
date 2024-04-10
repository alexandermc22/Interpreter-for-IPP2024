<?php

namespace IPP\Student\Exception;
use IPP\Core\Exception\IPPException;
use Throwable;
class MissingMemoryFrameError extends IPPException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct("Memory frame missing.", 55, $previous);
    }
}