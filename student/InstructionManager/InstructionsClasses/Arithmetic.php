<?php

namespace IPP\Student\InstructionManager\InstructionsClasses;

use IPP\Core\Exception\IPPException;
use IPP\Student\InstructionManager\JumpManager;
use IPP\Student\InstructionManager\FrameManager;
use IPP\Student\Exception\OperandTypeError;
use IPP\Student\Exception\InvalidOperandError;
use IPP\Student\Exception\StringOperationError;
class Arithmetic
{
    public static function handleInstruction(array $instruction, JumpManager $jumpManager,FrameManager $frameManager)
    {  
        try
        {
            $arg1 = $instruction['args']['arg1'];
            $arg2 = $instruction['args']['arg2'];
            switch ($instruction['opcode']) {
                case 'ADD':
                    $arg3 = $instruction['args']['arg3'];
                    if(self::checkArgument($arg2,'int',$frameManager) && self::checkArgument($arg3,'int',$frameManager))
                    {
                        $result= intval($frameManager->getValue($arg2)) + intval($frameManager->getValue($arg3));
                        $frameManager->updateVariable($arg1['value'],'int',$result);
                    }
                    else
                    {
                        throw new OperandTypeError();
                    }
                    break;
                case 'SUB':
                    $arg3 = $instruction['args']['arg3'];
                    if(self::checkArgument($arg2,'int',$frameManager) && self::checkArgument($arg3,'int',$frameManager))
                    {
                        $result = intval($frameManager->getValue($arg2)) - intval($frameManager->getValue($arg3));
                        $frameManager->updateVariable($arg1['value'],'int',$result);
                    }
                    break;
                case 'MUL':
                    $arg3 = $instruction['args']['arg3'];
                    if(self::checkArgument($arg2,'int',$frameManager) && self::checkArgument($arg3,'int',$frameManager))
                    {
                        $result = intval($frameManager->getValue($arg2)) * intval($frameManager->getValue($arg3));
                        $frameManager->updateVariable($arg1['value'],'int',$result);
                    }
                    break;
                case 'IDIV':
                    $arg3 = $instruction['args']['arg3'];
                    if(self::checkArgument($arg2,'int',$frameManager) && self::checkArgument($arg3,'int',$frameManager))
                    {
                        if(intval($frameManager->getValue($arg3)===0))
                            throw new  InvalidOperandError();
                        $result = intdiv(intval($frameManager->getValue($arg2)),intval($frameManager->getValue($arg3)));
                        $frameManager->updateVariable($arg1['value'],'int',$result);
                    }
                    break;
                case 'LT':
                    $arg3 = $instruction['args']['arg3'];
                    // Проверяем типы аргументов
                    if ($frameManager->getType($arg2) === 'int' && $frameManager->getType($arg3) === 'int') {
                        // Сравнение для целых чисел
                        $result = intval($frameManager->getValue($arg2)) < intval($frameManager->getValue($arg3));
                    } elseif ($frameManager->getType($arg2) === 'bool' && $frameManager->getType($arg3) === 'bool') {
                        // Сравнение для булевых значений
                            $value2 = $frameManager->getValue($arg2) === 'true' ? 1 : 0;
                            $value3 = $frameManager->getValue($arg3) === 'true' ? 1 : 0;
                            $result = $value2 < $value3 ;
                    } elseif ($frameManager->getType($arg2) === 'string' && $frameManager->getType($arg3) === 'string') {
                        // Сравнение для строк
                        $result = strcmp($frameManager->getValue($arg2), $frameManager->getValue($arg3)) < 0;
                    } else {
                        throw new OperandTypeError();
                    }
                    $result= $result? 1 : 0;
                    $frameManager->updateVariable($arg1['value'], 'bool', $result);
                    break;                    
                case 'GT':
                    $arg3 = $instruction['args']['arg3'];
                    // Проверяем типы аргументов
                    if ($frameManager->getType($arg2) === 'int' && $frameManager->getType($arg3) === 'int') {
                        // Сравнение для целых чисел
                        $result = intval($frameManager->getValue($arg2)) > intval($frameManager->getValue($arg3));
                    } elseif ($frameManager->getType($arg2) === 'bool' && $frameManager->getType($arg3) === 'bool') {
                        // Сравнение для булевых значений
                            $value2 = $frameManager->getValue($arg2) === 'true' ? 1 : 0;
                            $value3 = $frameManager->getValue($arg3) === 'true' ? 1 : 0;
                            $result = $value2 > $value3;
                    } elseif ($$frameManager->getType($arg2) === 'string' && $frameManager->getType($arg3) === 'string') {
                        // Сравнение для строк
                        $result = strcmp($frameManager->getValue($arg2), $frameManager->getValue($arg3)) > 0;
                    } else {
                        throw new OperandTypeError();
                    }
                    $result= $result? 1 : 0;
                    $frameManager->updateVariable($arg1['value'], 'bool', $result);
                    break;
                case 'EQ':
                    $arg3 = $instruction['args']['arg3'];
                        // Проверяем типы аргументов
                    if ($frameManager->getType($arg2) === 'int' && $frameManager->getType($arg3) === 'int') {
                        // Сравнение для целых чисел
                        $result = intval($frameManager->getValue($arg2)) === intval($frameManager->getValue($arg3));
                    } elseif ($frameManager->getType($arg2) === 'bool' && $frameManager->getType($arg3) === 'bool') {
                        // Сравнение для булевых значений
                        $result = $frameManager->getValue($arg2) === $frameManager->getValue($arg3);
                    } elseif ($frameManager->getType($arg2)=== 'string' && $frameManager->getType($arg3) === 'string') {
                        // Сравнение для строк
                        $result = $frameManager->getValue($arg2) === $frameManager->getValue($arg3);
                    } else {
                        throw new OperandTypeError();
                    }
                     $result= $result? 1 : 0;
                    $frameManager->updateVariable($arg1['value'], 'bool', $result);
                    break;
                    case 'AND':
                        $arg3 = $instruction['args']['arg3'];
                        // Проверяем типы аргументов и преобразуем строковые значения в булевые
                        if (self::checkArgument($arg2, 'bool',$frameManager) && self::checkArgument($arg3, 'bool',$frameManager)) {
                            $value2 = $frameManager->getValue($arg2) === 'true' ? true : false;
                            $value3 = $frameManager->getValue($arg3) === 'true' ? true : false;
                            $result = $value2 && $value3;
                        } else {
                            throw new OperandTypeError();
                        }
                        $result= $result? 1 : 0;
                        $frameManager->updateVariable($arg1['value'], 'bool', $result);
                        break;
                    case 'OR':
                        $arg3 = $instruction['args']['arg3'];
                        // Проверяем типы аргументов и преобразуем строковые значения в булевые
                        if (self::checkArgument($arg2, 'bool',$frameManager) && self::checkArgument($arg3, 'bool',$frameManager)) {
                            $value2 = $frameManager->getValue($arg2) === 'true' ? true : false;
                            $value3 = $frameManager->getValue($arg3) === 'true' ? true : false;
                            $result = $value2 || $value3;
                        } else {
                            throw new OperandTypeError();
                        }
                        $result= $result? 1 : 0;
                        $frameManager->updateVariable($arg1['value'], 'bool', $result);
                        break;
                    case 'NOT':
                        $arg3 = $instruction['args']['arg3'];
                        // Проверяем тип аргумента и преобразуем строковое значение в булевое
                        if (self::checkArgument($arg2, 'bool',$frameManager)) {
                            $value2 = $frameManager->getValue($arg2) === 'true' ? true : false;
                            $result = !$value2;
                        } else {
                            throw new OperandTypeError();
                        }
                        $result= $result? 1 : 0;
                        $frameManager->updateVariable($arg1['value'], 'bool', $result);
                        break;
                case 'INT2CHAR':
                    if (self::checkArgument($arg2, 'int',$frameManager)) {
                        $charCode = intval($frameManager->getValue($arg2));
                        if ($charCode < 0 || $charCode > 0x10FFFF) {
                            throw new StringOperationError();
                        }
                        $char = mb_chr($charCode, 'UTF-8');
                        $frameManager->updateVariable($arg1['value'], 'string', $char);
                    } else {
                        throw new OperandTypeError();
                    }
                    break;
                case 'STRI2INT':
                    $arg3 = $instruction['args']['arg3'];
                    if (self::checkArgument($arg2, 'string',$frameManager) && self::checkArgument($arg3, 'int',$frameManager)) {
                        $str = $frameManager->getValue($arg2);
                        $index = intval($arg3['value']);
                        if ($index < 0 || $index >= mb_strlen($str, 'UTF-8')) {
                            throw new StringOperationError();
                        }
                        $char = mb_substr($str, $index, 1, 'UTF-8');
                        $charCode = mb_ord($char, 'UTF-8');
                        $frameManager->updateVariable($arg1['value'], 'int', $charCode);
                    } else {
                        throw new OperandTypeError();
                    }
                    break;
                }
        }
        catch(IPPException $e)
        {
            throw $e;
        }
    }


    public static function checkArgument(array $arg, string $expectedType,FrameManager $frameManager): bool {
        try
        {
            switch ($expectedType) {
                case 'string':
                    return $frameManager->getType($arg) === 'string';
                case 'int':
                    return $frameManager->getType($arg) === 'int';
                case 'label':
                    return $frameManager->getType($arg)=== 'label'; // Лейбл может быть только строкой
                case 'bool':
                    return $frameManager->getType($arg) === 'bool'; 
                default:
                    throw new OperandTypeError();
            }
        }
        catch(IPPException $e)
        {
            throw $e;
        }
    }
}
