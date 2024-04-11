<?php

namespace IPP\Student;

use IPP\Core\AbstractInterpreter;
use IPP\Student\InstructionManager\JumpManager;
use IPP\Core\Exception\IPPException;
use IPP\Student\InstructionManager\InstructionManager;

class Interpreter extends AbstractInterpreter
{
    public function execute(): int
    {

        try {
            //get document
            $sourceReader = $this->source->getDOMDocument();
            //init managers
            $jumpManager = new JumpManager($sourceReader);
            $instructionManager = new InstructionManager();
            while ($instruction = $jumpManager->getNextInstruction()) {
                $result = $instructionManager->parseInstruction($instruction, $jumpManager, $this->input, $this->stdout, $this->stderr);
                if ($result >= 0 && $result <= 9) {
                    return $result;
                }
            }
        } catch (IPPException $e) {
            throw $e;
        }
        return 0;
    }
}
