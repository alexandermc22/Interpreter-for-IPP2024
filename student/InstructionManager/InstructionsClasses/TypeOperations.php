<?php

namespace IPP\Student\InstructionManager\InstructionsClasses;
use IPP\Student\InstructionManager\FrameManager;
use IPP\Core\Exception\IPPException;
class TypeOperations
{
    public static function handleInstruction(array $instruction, FrameManager $frameManager)
    {
        try
        {
            $arg1 = $instruction['args']['arg1'];
            $arg2 = $instruction['args']['arg2'];
            $type = $frameManager->getType($arg2);
            if ($type === 'var')
            {
                $type = '';
            }
            $frameManager->updateVariable($arg1['value'],'string',$type);
        }
        catch(IPPException $e)
        {
            throw $e;
        }
    }
}