<?php

namespace IPP\Student\InstructionManager\InstructionsClasses;
use IPP\Student\InstructionManager\FrameManager;
use IPP\Student\InstructionManager\StackManager;
use IPP\Core\Exception\IPPException;
class DataStack
{
    public static function handleInstruction(array $instruction, StackManager $stackManager,FrameManager $frameManager)
    {
        $arg1 = $instruction['args']['arg1'];
        try
        {
            if ($instruction['opcode'] === 'PUSHS')
            {
                $stackManager->push($arg1,$frameManager);
            }
            else
            {
                $stackManager->pop($arg1,$frameManager);
            }
        }
        catch(IPPException $e)
        {
            throw $e;
        }
    }
}