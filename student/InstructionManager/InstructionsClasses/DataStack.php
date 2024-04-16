<?php

namespace IPP\Student\InstructionManager\InstructionsClasses;
use IPP\Student\InstructionManager\FrameManager;
use IPP\Student\InstructionManager\StackManager;
use IPP\Core\Exception\IPPException;
class DataStack
{
    /**
     * Handle control flow instructions such as LABEL, JUMP, JUMPIFEQ, JUMPIFNEQ, and EXIT.
     *
     * @param array<string, mixed> $instruction The instruction to parse.
     * @param StackManager $stackManager The JumpManager instance.
     * @param FrameManager $frameManager The FrameManager instance.
     * @throws IPPException If an IPP exception occurs during handling the instruction.
     */
    public static function handleInstruction(array $instruction, StackManager $stackManager,FrameManager $frameManager):void
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