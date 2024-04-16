<?php

namespace IPP\Student\InstructionManager\InstructionsClasses;
use IPP\Core\Exception\IPPException;
use IPP\Core\Interface\InputReader;
use IPP\Core\Interface\OutputWriter;
use IPP\Student\InstructionManager\FrameManager;
use IPP\Student\Exception\OperandTypeError;
use IPP\Core\StreamWriter;
class IO
{
    public static function handleInstruction(array $instruction, InputReader $inputReader,FrameManager $frameManager,OutputWriter $streamWriter)
    {
        $arg1 = $instruction['args']['arg1'];
        
        try{
            if ($instruction['opcode'] === 'READ')
            {
                $arg2 = $instruction['args']['arg2'];
                switch ($arg2)
                {
                    case 'int':
                        $var = $inputReader->readInt();
                        if ($var === null)
                        {
                            $frameManager->updateVariable($arg1['value'],'nil','nil');
                        }
                        else
                        {
                            $frameManager->updateVariable($arg1['value'],'int',$var);
                        }
                        break;
                    
                    case 'string':
                        $var = $inputReader->readString();
                        if ($var === null)
                        {
                            $frameManager->updateVariable($arg1['value'],'nil','nil');
                        }
                        else
                        {
                            $frameManager->updateVariable($arg1['value'],'string',$var);
                        }
                        break;
                    case 'bool':
                        $var = $inputReader->readBool();
                        if ($var === null)
                        {
                            $frameManager->updateVariable($arg1['value'],'nil','nil');
                        }
                        else
                        {
                            $frameManager->updateVariable($arg1['value'],'bool',$var);
                        }
                        break;
                    default:
                    throw new OperandTypeError();
                }
            }
            elseif($instruction['opcode'] === 'WRITE')
            {
                switch($frameManager->gettype($arg1))
                {
                    case 'int':
                        $streamWriter->writeInt($frameManager->getValue($arg1));
                        break;
                    case 'string':
                        $streamWriter->writeString($frameManager->getValue($arg1));
                        break;
                    case 'bool':
                        $streamWriter->writeBool($frameManager->getValue($arg1));
                        break;
                    case 'nil':
                        $streamWriter->writeString('');
                        break;
                    default:
                        throw new OperandTypeError();
                }
            }
        }
        catch(IPPException $e)
        {
            throw $e;
        }
    }
}