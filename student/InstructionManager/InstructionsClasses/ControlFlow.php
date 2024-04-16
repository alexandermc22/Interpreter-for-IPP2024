<?php

namespace IPP\Student\InstructionManager\InstructionsClasses;

use IPP\Student\InstructionManager\JumpManager;
use IPP\Student\InstructionManager\FrameManager;
use IPP\Core\Exception\IPPException;
use IPP\Student\Exception\OperandTypeError;
use IPP\Student\Exception\InvalidOperandError;
use IPP\Student\Exception\UndefinedLabelError;

class ControlFlow
{
    /**
     * Handle control flow instructions such as LABEL, JUMP, JUMPIFEQ, JUMPIFNEQ, and EXIT.
     *
     * @param array<string, mixed> $instruction The instruction to parse.
     * @param JumpManager $jumpManager The JumpManager instance.
     * @param FrameManager $frameManager The FrameManager instance.
     * @return int The result of the control flow operation if applicable, or -1 if no result.
     * @throws IPPException If an IPP exception occurs during handling the instruction.
     */
    public static function handleInstruction(array $instruction, JumpManager $jumpManager, FrameManager $frameManager): int
    {
        try {
            $arg1 = $instruction['args']['arg1'];
            switch ($instruction['opcode']) {
                case 'LABEL':
                    if ($frameManager->getType($arg1) !== 'label') {
                        throw new OperandTypeError();
                    }
                    if ($jumpManager->isExist($frameManager->getValue($arg1))) {
                        throw new UndefinedLabelError();
                    }
                    break;
                case 'JUMP':
                    if ($frameManager->getType($arg1) !== 'label') {
                        throw new OperandTypeError();
                    }
                    if ($jumpManager->isExist($frameManager->getValue($arg1))) {
                        throw new UndefinedLabelError();
                    }
                    $jumpManager->jump($frameManager->getValue($arg1));
                    break;
                case 'JUMPIFEQ':
                    if ($frameManager->getType($arg1) !== 'label') {
                        throw new OperandTypeError();
                    }
                    if ($jumpManager->isExist($frameManager->getValue($arg1))) {
                        throw new UndefinedLabelError();
                    }
                    $arg2 = $instruction['args']['arg2'];
                    $arg3 = $instruction['args']['arg3'];
                    if ($frameManager->getType($arg2) === 'int' && $frameManager->getType($arg3) === 'int') {
                        $result = intval($frameManager->getValue($arg2)) === intval($frameManager->getValue($arg3));
                    } elseif ($frameManager->getType($arg2) === 'bool' && $frameManager->getType($arg3) === 'bool') {
                        $result = $frameManager->getValue($arg2) === $frameManager->getValue($arg3);
                    } elseif ($frameManager->getType($arg2) === 'string' && $frameManager->getType($arg3) === 'string') {
                        $result = $frameManager->getValue($arg2) === $frameManager->getValue($arg3);
                    } else {
                        throw new OperandTypeError();
                    }
                    $result = $result ? 1 : 0;
                    if ($result) {
                        $jumpManager->jump($frameManager->getValue($arg1));
                    }
                    break;
                case 'JUMPIFNEQ':
                    if ($frameManager->getType($arg1) !== 'label') {
                        throw new OperandTypeError();
                    }
                    if ($jumpManager->isExist($frameManager->getValue($arg1))) {
                        throw new UndefinedLabelError();
                    }
                    $arg2 = $instruction['args']['arg2'];
                    $arg3 = $instruction['args']['arg3'];
                    if ($frameManager->getType($arg2) === 'int' && $frameManager->getType($arg3) === 'int') {
                        $result = intval($frameManager->getValue($arg2)) === intval($frameManager->getValue($arg3));
                    } elseif ($frameManager->getType($arg2) === 'bool' && $frameManager->getType($arg3) === 'bool') {
                        $result = $frameManager->getValue($arg2) === $frameManager->getValue($arg3);
                    } elseif ($frameManager->getType($arg2) === 'string' && $frameManager->getType($arg3) === 'string') {
                        // Сравнение для строк
                        $result = $frameManager->getValue($arg2) === $frameManager->getValue($arg3);
                    } else {
                        throw new OperandTypeError();
                    }
                    $result = $result ? 1 : 0;
                    if (!$result) {
                        $jumpManager->jump($frameManager->getValue($arg1));
                    }
                    break;

                case 'EXIT':
                    if ($frameManager->getType($arg1) !== 'int') {
                        throw new OperandTypeError();
                    }
                    $result = $frameManager->getValue($arg1);
                    if ($result < 0 || $result > 9) {
                        throw new InvalidOperandError();
                    }
                    return $result;
            }
            return -1;
        } catch (IPPException $e) {
            throw $e;
        }
    }
}
