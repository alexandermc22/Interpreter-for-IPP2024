<?php

namespace IPP\Student;
use IPP\Student\Exception\UnexpectedXMLError;
use Exception;
use IPP\Core\AbstractInterpreter;
use IPP\Core\Exception\NotImplementedException;
use IPP\Core\Exception\XMLException;
use IPP\Student\InstructionManager\FrameManager;
use Throwable;
use IPP\Student\InstructionManager\JumpManager;
use IPP\Student\XmlInterpreter\XmlInterpreter;
use IPP\Core\Exception\IPPException;
use IPP\Student\InstructionManager\InstructionManager;

class Interpreter extends AbstractInterpreter
{
    public function execute(): int
    {
        $sourceReader = $this->source->getDOMDocument();
        $inputReader = $this->input;

        try
        {
            $jumpManager = new JumpManager($sourceReader);
            $instructionManager = new InstructionManager();
            while ($instruction = $jumpManager->getNextInstruction()) 
            {
                $instructionManager->parseInstruction($instruction,$jumpManager,$this->input,$this->stdout);
            }
        }
        catch (IPPException $e)
        {
            throw $e;
        }
        
        // $this->stdout->writeString("stdout");
        // $this->stderr->writeString("stderr");
        // throw new NotImplementedException;
        return 0;
    }
}
