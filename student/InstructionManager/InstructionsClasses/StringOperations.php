<?php

namespace IPP\Student\InstructionManager\InstructionsClasses;
use IPP\Student\InstructionManager\FrameManager;
use IPP\Core\Exception\IPPException;
use IPP\Student\Exception\OperandTypeError;
use IPP\Student\Exception\InvalidOperandError;
use IPP\Student\Exception\StringOperationError;
class StringOperations
{
    public static function handleInstruction(array $instruction, FrameManager $frameManager)
    {
        try
        {
            $arg1 = $instruction['args']['arg1'];
            $arg2 = $instruction['args']['arg2'];
            switch ($instruction['opcode']) {
                case 'CONCAT':
                    $arg3 = $instruction['args']['arg3'];
                    $value1 = $frameManager->getValue($arg2);
                    $type1 = $frameManager->gettype($arg2);
                    $value2 = $frameManager->getValue($arg3);
                    $type2 = $frameManager->gettype($arg3);
                    if($type1 !== 'string' || $type2 !== 'string')
                    {
                        throw new OperandTypeError();  
                    }
                    $result = $value1 . $value2;
                    $frameManager->updateVariable($arg1['value'],'string',$result);
                    break;
                case 'STRLEN':
                $value = $frameManager->getValue($arg2);
                $type = $frameManager->getType($arg2);

                if ($type !== 'string') {
                    throw new OperandTypeError();
                }

                // Получаем длину строки
                $length = mb_strlen($value);  // Используем mb_strlen для корректной работы с многобайтовыми символами

                // Обновляем переменную с длиной строки
                $frameManager->updateVariable($arg1['value'], 'int', $length);
                break;
                case 'GETCHAR':
                    $arg3 = $instruction['args']['arg3'];
                    $value = $frameManager->getValue($arg2);
                    $type = $frameManager->getType($arg2);
                    $index = $frameManager->getValue($arg3);
                    $indexType = $frameManager->getType($arg3);
                
                    if ($type !== 'string' || $indexType !== 'int') {
                        throw new OperandTypeError();
                    }
                
                    // Проверяем, что индекс находится в допустимых пределах
                    if ($index < 0 || $index >= mb_strlen($value)) {
                        throw new StringOperationError();
                    }
                
                    // Получаем символ по указанному индексу
                    $char = mb_substr($value, $index, 1);  // Используем mb_substr для корректной работы с многобайтовыми символами
                
                    // Обновляем переменную с полученным символом
                    $frameManager->updateVariable($arg1['value'], 'string', $char);
                    break;
                case 'SETCHAR':
                    $arg3 = $instruction['args']['arg3'];
                    $value = $frameManager->getValue($arg2);
                    $type = $frameManager->getType($arg2);
                    $index = $frameManager->getValue($arg3);
                    $indexType = $frameManager->getType($arg3);
                    $char = $frameManager->getValue($arg1);  // Получаем значение уже изменяемой строки
                    $charType = $frameManager->getType($arg1);

                    if ($type !== 'string' || $indexType !== 'int' || $charType !== 'string') {
                        throw new OperandTypeError();
                    }

                    // Проверяем, что индекс находится в допустимых пределах строки
                    if ($index < 0 || $index >= mb_strlen($value) || mb_strlen($char) === 0) {
                        throw new StringOperationError();
                    }

                    // Модифицируем символ в строке
                    $charLength = mb_strlen($char);
                    $beforeChar = mb_substr($char, 0, $index);
                    $afterChar = mb_substr($char, $index + 1, $charLength - $index - 1);
                    $char = $beforeChar . $value . $afterChar;

                    // Обновляем переменную с измененным символом
                    $frameManager->updateVariable($arg1['value'], 'string', $char);
                    break;
            }
        }
        catch(IPPException $e)
        {
            throw $e;
        }
    }
}