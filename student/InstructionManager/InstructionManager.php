<?php

namespace IPP\Student\InstructionManager;

use IPP\Core\Interface\InputReader;
use IPP\Core\Interface\OutputWriter;
use IPP\Core\Interface\SourceReader;
use IPP\Core\FileInputReader;
use IPP\Core\StreamWriter;
use IPP\Student\InstructionManager\FrameManager;
use IPP\Student\InstructionManager\StackManager;
use IPP\Student\InstructionManager\InstructionsClasses\Arithmetic;
use IPP\Student\InstructionManager\InstructionsClasses\ControlFlow;
use IPP\Student\InstructionManager\InstructionsClasses\DataStack;
use IPP\Student\InstructionManager\InstructionsClasses\Debug;
use IPP\Student\InstructionManager\InstructionsClasses\IO;
use IPP\Student\InstructionManager\InstructionsClasses\MemoryFrame;
use IPP\Student\InstructionManager\InstructionsClasses\StringOperations;
use IPP\Student\InstructionManager\InstructionsClasses\TypeOperations;
use IPP\Student\Exception\OperandTypeError;

class InstructionManager
{
    protected FrameManager $frameManager;
    protected StackManager $stackManager;
    protected array $MemoryFrameArray = [];
    protected array $DataStackArray = [];
    protected array $ArithmeticArray = [];
    protected array $IOArray = [];
    protected array $StringArray = [];
    protected array $TypeArray = [];
    protected array $ControlFlowArray = [];
    protected array $DebugArray = [];

    public function __construct()
    {
        $this->frameManager = new FrameManager();
        $this->stackManager = new StackManager();

        // Initialize arrays with instruction groups
        $this->MemoryFrameArray = ['MOVE', 'CREATEFRAME', 'PUSHFRAME', 'POPFRAME', 'DEFVAR', 'RETURN', 'CALL'];
        $this->DataStackArray = ['PUSHS', 'POPS'];
        $this->ArithmeticArray = ['ADD', 'SUB', 'MUL', 'IDIV', 'LT', 'GT', 'EQ', 'AND', 'OR', 'NOT', 'INT2CHAR', 'STRI2INT'];
        $this->IOArray = ['READ', 'WRITE'];
        $this->StringArray = ['CONCAT', 'STRLEN', 'GETCHAR', 'SETCHAR'];
        $this->TypeArray = ['TYPE'];
        $this->ControlFlowArray = ['LABEL', 'JUMP', 'JUMPIFEQ', 'JUMPIFNEQ', 'EXIT'];
        $this->DebugArray = ['DPRINT', 'BREAK'];
    }

    /**
     * Parse and handle the given instruction.
     *
     * @param array $instruction The instruction to parse.
     * @param JumpManager $jumpManager The jump manager.
     * @param InputReader $inputReader The input reader.
     * @param OutputWriter $streamWriter The stream writer for output.
     * @param OutputWriter $errorWriter The stream writer for error messages.
     * @return int The result of handling the instruction.
     * @throws OperandTypeError If an invalid operand type is encountered.
     */
    public function parseInstruction(array $instruction, JumpManager $jumpManager, InputReader $inputReader, OutputWriter $streamWriter, OutputWriter $errorWriter): int
    {
        $opcode = $instruction['opcode'];
        if (in_array($opcode, $this->MemoryFrameArray)) {
            MemoryFrame::handleInstruction($instruction, $jumpManager, $this->frameManager);
        } elseif (in_array($opcode, $this->DataStackArray)) {
            DataStack::handleInstruction($instruction, $this->stackManager, $this->frameManager);
        } elseif (in_array($opcode, $this->ArithmeticArray)) {
            Arithmetic::handleInstruction($instruction, $jumpManager, $this->frameManager);
        } elseif (in_array($opcode, $this->IOArray)) {
            IO::handleInstruction($instruction, $inputReader, $this->frameManager, $streamWriter);
        } elseif (in_array($opcode, $this->StringArray)) {
            StringOperations::handleInstruction($instruction, $this->frameManager);
        } elseif (in_array($opcode, $this->TypeArray)) {
            TypeOperations::handleInstruction($instruction, $this->frameManager);
        } elseif (in_array($opcode, $this->ControlFlowArray)) {
            $result = ControlFlow::handleInstruction($instruction, $jumpManager, $this->frameManager);
            if ($result >= 0 && $result <= 9) {
                return $result;
            }
        } elseif (in_array($opcode, $this->DebugArray)) {
            Debug::handleInstruction($instruction, $this->frameManager, $errorWriter);
        } else {
            throw new OperandTypeError();
        }
        return -1;
    }
}
