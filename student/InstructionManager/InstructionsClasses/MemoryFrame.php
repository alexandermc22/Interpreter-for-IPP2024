<?php

namespace IPP\Student\InstructionManager\InstructionsClasses;
use IPP\Student\InstructionManager\JumpManager;
use IPP\Core\Exception\IPPException;
use IPP\Student\InstructionManager\FrameManager;
use IPP\Student\Exception\OperandTypeError;
class MemoryFrame
{
    public static function handleInstruction(array $instruction, JumpManager $jumpManager,FrameManager $frameManager)
    {
        try
        {
            switch ($instruction['opcode']) {
                case 'MOVE':
                    $arg1 = $instruction['args']['arg1'];
                    $arg2 = $instruction['args']['arg2'];
                    $value = $frameManager->getValue($arg2);
                    $type = $frameManager->getType($arg2);
                    $frameManager->updateVariable($arg1['value'],$type,$value);
                    break;

                case 'DEFVAR':
                    $arg1 = $instruction['args']['arg1'];
                    $frameManager->initializeVariable($arg1['value'],$arg1['type']);
                    break;
                case 'CREATEFRAME':
                    $frameManager->createFrame();
                    break;
                case 'PUSHFRAME':
                    $frameManager->pushFrame();
                    break;
                case 'POPFRAME':
                    $frameManager->popFrame();
                    break;
                case 'CALL':
                    $arg1 = $instruction['args']['arg1'];
                    $value = $frameManager->getValue($arg1);
                    $type = $frameManager->getType($arg1);
                    if($type === 'label')
                    {
                        $jumpManager->call($value);
                    }
                    else
                    {
                        throw new OperandTypeError();
                    }
                    break;
                    case 'RETURN':
                        $jumpManager->return();
                        break;
            }
        }
        catch(IPPException $e)
        {
            throw $e;
        }
    }
}