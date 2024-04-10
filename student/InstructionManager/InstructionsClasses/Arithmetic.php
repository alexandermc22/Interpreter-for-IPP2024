<?php

namespace IPP\Student\InstructionManager\InstructionsClasses;
use IPP\Student\InstructionManager\JumpManager;
class Arithmetic
{
    public static function handleInstruction(array $instruction, JumpManager $jumpManager)
    {
        switch ($instruction['opcode']) {
            case 'ADD':
                // Обработка инструкции ADD
                break;
            case 'SUB':
                // Обработка инструкции SUB
                break;
            case 'MUL':
                // Обработка инструкции MUL
                break;
            case 'IDIV':
                // Обработка инструкции IDIV
                break;
            case 'LT':
                // Обработка инструкции LT
                break;
            case 'GT':
                // Обработка инструкции GT
                break;
            case 'EQ':
                // Обработка инструкции EQ
                break;
            case 'AND':
                // Обработка инструкции AND
                break;
            case 'OR':
                // Обработка инструкции OR
                break;
            case 'NOT':
                // Обработка инструкции NOT
                break;
            case 'INT2CHAR':
                // Обработка инструкции INT2CHAR
                break;
            case 'STRI2INT':
                // Обработка инструкции STRI2INT
                break;
            }
    }
}
