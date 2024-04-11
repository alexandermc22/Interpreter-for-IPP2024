<?php

namespace IPP\Student\InstructionManager\InstructionsClasses;

use IPP\Student\InstructionManager\FrameManager;
use IPP\Core\Exception\IPPException;
use IPP\Student\Exception\OperandTypeError;
use IPP\Student\Exception\StringOperationError;

class StringOperations
{
    /**
     * Handles string-related instructions such as CONCAT, STRLEN, GETCHAR, and SETCHAR.
     *
     * @param array $instruction The instruction to be processed.
     * @param FrameManager $frameManager An instance of FrameManager.
     * @throws IPPException If an IPP exception occurs during instruction processing.
     */
    public static function handleInstruction(array $instruction, FrameManager $frameManager)
    {
        try {
            $arg1 = $instruction['args']['arg1'];
            $arg2 = $instruction['args']['arg2'];

            switch ($instruction['opcode']) {
                case 'CONCAT':
                    // Concatenate two strings and store the result
                    $arg3 = $instruction['args']['arg3'];
                    $value1 = $frameManager->getValue($arg2);
                    $type1 = $frameManager->getType($arg2);
                    $value2 = $frameManager->getValue($arg3);
                    $type2 = $frameManager->getType($arg3);

                    // Check if both arguments are strings
                    if ($type1 !== 'string' || $type2 !== 'string') {
                        throw new OperandTypeError();
                    }

                    // Perform string concatenation and update the variable
                    $result = $value1 . $value2;
                    $frameManager->updateVariable($arg1['value'], 'string', $result);
                    break;

                case 'STRLEN':
                    // Get the length of a string and update the variable
                    $value = $frameManager->getValue($arg2);
                    $type = $frameManager->getType($arg2);

                    // Check if the argument is a string
                    if ($type !== 'string') {
                        throw new OperandTypeError();
                    }

                    // Get the length of the string using mb_strlen for multibyte character support
                    $length = mb_strlen($value);
                    $frameManager->updateVariable($arg1['value'], 'int', $length);
                    break;

                case 'GETCHAR':
                    // Get a character from a string at the specified index
                    $arg3 = $instruction['args']['arg3'];
                    $value = $frameManager->getValue($arg2);
                    $type = $frameManager->getType($arg2);
                    $index = $frameManager->getValue($arg3);
                    $indexType = $frameManager->getType($arg3);

                    // Check if both arguments are of correct types
                    if ($type !== 'string' || $indexType !== 'int') {
                        throw new OperandTypeError();
                    }

                    // Check if the index is within bounds
                    if ($index < 0 || $index >= mb_strlen($value)) {
                        throw new StringOperationError();
                    }

                    // Get the character at the specified index using mb_substr for multibyte character support
                    $char = mb_substr($value, $index, 1);
                    $frameManager->updateVariable($arg1['value'], 'string', $char);
                    break;

                case 'SETCHAR':
                    // Set a character in a string at the specified index
                    $arg3 = $instruction['args']['arg3'];
                    $value = $frameManager->getValue($arg2);
                    $type = $frameManager->getType($arg2);
                    $index = $frameManager->getValue($arg3);
                    $indexType = $frameManager->getType($arg3);
                    $char = $frameManager->getValue($arg1);  // Get the value of the string to be modified
                    $charType = $frameManager->getType($arg1);

                    // Check if all arguments are of correct types
                    if ($type !== 'string' || $indexType !== 'int' || $charType !== 'string') {
                        throw new OperandTypeError();
                    }

                    // Check if the index is within bounds and the character value is not empty
                    if ($index < 0 || $index >= mb_strlen($value) || mb_strlen($char) === 0) {
                        throw new StringOperationError();
                    }

                    // Modify the character in the string
                    $charLength = mb_strlen($char);
                    $beforeChar = mb_substr($char, 0, $index);
                    $afterChar = mb_substr($char, $index + 1, $charLength - $index - 1);
                    $char = $beforeChar . $value . $afterChar;

                    // Update the variable with the modified character
                    $frameManager->updateVariable($arg1['value'], 'string', $char);
                    break;
            }
        } catch (IPPException $e) {
            throw $e;
        }
    }
}
