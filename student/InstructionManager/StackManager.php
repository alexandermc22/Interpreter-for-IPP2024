<?php

namespace IPP\Student\InstructionManager;


use IPP\Core\Exception\IPPException;
use IPP\Student\Exception\MissingValueError;


class StackManager
{

    /**
     * @var array<mixed,string>[] Stack array containing elements.
     * Each element in the stack is represented as an associative array with keys 'type' and 'value'.
     */
    private $stack;

    public function __construct()
    {
        $this->stack = [];
    }

    /** @param array{name: string, type: string, value: mixed} $arg args **/
    public function push(array $arg,FrameManager $frameManager):void
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
/** @param array{name: string, type: string, value: mixed} $arg args **/
    public function pop(array $arg,FrameManager $frameManager):void
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

    /** @return array<mixed,string> **/
    public function top(): array
    {
        if ($this->isEmpty()) {
            throw new MissingValueError();
        }
        return end($this->stack);
    }

    public function isEmpty() : bool
    {
        return empty($this->stack);
    }
}