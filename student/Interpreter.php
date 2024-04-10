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


        try
        {
            $jumpManager = new JumpManager($sourceReader);
            $instructionManager = new InstructionManager();
            while ($instruction = $jumpManager->getNextInstruction()) 
            {
                $instructionManager->parseInstruction($instruction,$jumpManager);
            }
        }
        catch (IPPException $e)
        {
            throw $e;
        }
        // $inputReader = $this->input->readString();
        // $this->stdout->writeString("stdout");
        // $this->stderr->writeString("stderr");
        // throw new NotImplementedException;
        return 0;
    }
}
