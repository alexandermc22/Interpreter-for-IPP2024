<?php

namespace IPP\Student\InstructionManager\InstructionsClasses;
use IPP\Student\InstructionManager\FrameManager;
use IPP\Core\StreamWriter;
use IPP\Core\Exception\IPPException;
use IPP\Student\Exception\OperandTypeError;

class Debug
{
    public static function handleInstruction(array $instruction, FrameManager $frameManager,StreamWriter $errorWriter)
    {
        try
        {
            if ($instruction['opcode'] === 'DPRINT')
            {
                $arg1 = $instruction['args']['arg1'];
               switch($frameManager->gettype($arg1))
                {
                    case 'int':
                        $errorWriter->writeInt($frameManager->getValue($arg1));
                        break;
                    case 'string':
                        $errorWriter->writeString($frameManager->getValue($arg1));
                        break;
                    case 'bool':
                        $errorWriter->writeBool($frameManager->getValue($arg1));
                        break;
                    case 'nil':
                        $errorWriter->writeString('');
                        break;
                    default:
                        throw new OperandTypeError();
                }
            }
            else
            {
                $errorWriter->writeString('Order:');
                $errorWriter->writeInt($instruction['order']);
                $errorWriter->writeString('GlobalFrame:');
                $errorWriter->writeString(print_r($frameManager->getGlobalFrame(), true));
                $errorWriter->writeString('LocalFrames:');
                $errorWriter->writeString(print_r($frameManager->getLocalFrames(), true));
                $errorWriter->writeString('TemporaryFrame:');
                $errorWriter->writeString(print_r($frameManager->getTemporaryFrame(), true));
            }
        }
        catch(IPPException $e)
        {
            throw $e;
        }        
    }
}