<?php

namespace IPP\Student\InstructionManager;

use Exception;
use IPP\Core\AbstractInterpreter;
use IPP\Core\Exception\NotImplementedException;
use IPP\Core\Exception\XMLException;
use IPP\Student\XmlInterpreter\XmlInterpreter;
use IPP\Core\Exception\IPPException;
use IPP\Student\Exception\UndefinedLabelError;
use IPP\Student\Exception\MissingMemoryFrameError;
use IPP\Student\Exception\NonExistingVariableError;
use IPP\Student\Exception\MissingValueError;


class StackManager
{
    private $stack;

    public function __construct()
    {
        $this->stack = [];
    }

    public function push(array $arg,FrameManager $frameManager)
    {
        try
        {
            $value = $frameManager->getValue($arg);
            $type = $frameManager->getType($arg);
            array_push($this->stack, ['value' => $value, 'type' => $type]);
        }
        catch (IPPException $e)
        {
            throw $e;
        }
    }

    public function pop(array $arg,FrameManager $frameManager)
    {
        try
        {
        if ($this->isEmpty()) {
            throw new MissingValueError();
        }
        $var = array_pop($this->stack);
        $frameManager->updateVariable($arg['value'],$var['type'],$var['value']);
        }
        catch (IPPException $e)
        {
            throw $e;
        }
    }

    public function top()
    {
        if ($this->isEmpty()) {
            throw new MissingValueError();
        }
        return end($this->stack);
    }

    public function isEmpty()
    {
        return empty($this->stack);
    }
}