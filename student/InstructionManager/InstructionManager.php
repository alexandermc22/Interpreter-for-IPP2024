<?php

namespace IPP\Student\InstructionManager;

use Exception;
use IPP\Core\AbstractInterpreter;
use IPP\Core\FileInputReader;
use IPP\Core\StreamWriter;
use IPP\Core\Exception\NotImplementedException;
use IPP\Core\Exception\XMLException;
use IPP\Student\XmlInterpreter\XmlInterpreter;
use IPP\Core\Exception\IPPException;
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
                // Подпункт 5.4.1
                $this->MemoryFrameArray = ['MOVE', 'CREATEFRAME', 'PUSHFRAME', 'POPFRAME','DEFVAR','RETURN','CALL'];

                // Подпункт 5.4.2
                $this->DataStackArray = ['PUSHS', 'POPS'];
        
                // Подпункт 5.4.3
                $this->ArithmeticArray = ['ADD', 'SUB', 'MUL', 'IDIV', 'LT', 'GT', 'EQ', 'AND', 'OR', 'NOT', 'INT2CHAR', 'STRI2INT'];
        
                // Подпункт 5.4.4
                $this->IOArray = ['READ', 'WRITE'];
        
                // Подпункт 5.4.5
                $this->StringArray = ['CONCAT', 'STRLEN', 'GETCHAR', 'SETCHAR'];
        
                // Подпункт 5.4.6
                $this->TypeArray = ['TYPE'];
        
                // Подпункт 5.4.7
                $this->ControlFlowArray = ['LABEL', 'JUMP', 'JUMPIFEQ', 'JUMPIFNEQ', 'EXIT'];
        
                // Подпункт 5.4.8
                $this->DebugArray = ['DPRINT', 'BREAK'];
    }
    
    public function parseInstruction(array $instruction,JumpManager $jumpManager,FileInputReader $inputReader,StreamWriter $streamWriter)
    {
        // $this->frameManager->initializeVariable('GF@abc','var');
        // // print_r( $this->frameManager->getGlobalFrame());
        // $this->frameManager->updateVariable('GF@abc','int',3);
        // // print_r( $this->frameManager->getGlobalFrame());
        // $this->frameManager->initializeVariable('GF@bbb','var');
        // $this->frameManager->updateVariable('GF@bbb','int',5);
        // // print_r( $this->frameManager->getGlobalFrame());

        $opcode = $instruction['opcode'];
        
        //     echo $opcode;
        //     echo '  ';
        if (in_array($opcode, $this->MemoryFrameArray)) {
            MemoryFrame::handleInstruction($instruction, $jumpManager,$this->frameManager);
        } elseif (in_array($opcode, $this->DataStackArray)) {
            DataStack::handleInstruction($instruction, $this->stackManager,$this->frameManager);
        } elseif (in_array($opcode, $this->ArithmeticArray)) {
            Arithmetic::handleInstruction($instruction, $jumpManager,$this->frameManager);
        } elseif (in_array($opcode, $this->IOArray)) {
            IO::handleInstruction($instruction, $inputReader,$this->frameManager, $streamWriter);
        } elseif (in_array($opcode, $this->StringArray)) {
            StringOperations::handleInstruction($instruction, $this->frameManager);
        } elseif (in_array($opcode, $this->TypeArray)) {
            TypeOperations::handleInstruction($instruction,  $this->frameManager);
        } elseif (in_array($opcode, $this->ControlFlowArray)) {
            ControlFlow::handleInstruction($instruction, $jumpManager);
        } elseif (in_array($opcode, $this->DebugArray)) {
            Debug::handleInstruction($instruction, $jumpManager);
        } else {
            throw new \Exception("Unknown opcode: $opcode");
        }
        // $streamWriter->writeBool($this->frameManager->getValue($instruction['args']['arg1']));
        // print_r( $this->frameManager->getGlobalFrame());
    }
}