<?php

namespace IPP\Student\InstructionManager;

use IPP\Student\Exception\UnexpectedXMLError;
use Exception;
use Throwable;
use IPP\Core\AbstractInterpreter;
use IPP\Core\Exception\NotImplementedException;
use IPP\Core\Exception\XMLException;
use IPP\Student\XmlInterpreter\XmlInterpreter;
use IPP\Core\Exception\IPPException;
use IPP\Student\Exception\MissingMemoryFrameError;
use IPP\Student\Exception\UndefinedLabelError;

class JumpManager
{
    protected array $interpreterStack = [];
    protected \DOMDocument $xmlDocument;
    protected array $processedLabels = [];

    public function __construct(\DOMDocument $xmlDocument)
    {
        try {
            $this->xmlDocument = $xmlDocument;
            $instructions = new XmlInterpreter($xmlDocument);
            $this->interpreterStack[] = $instructions;
        } catch (IPPException $e) {
            throw $e;
        }
    }

    public function isExist(string $label): bool
    {
        if (in_array($label, $this->processedLabels)) {
            return true;
        } else {
            return false;
        }
    }

    public function getNextInstruction(): ?array
    {
        try {
            $instruction = end($this->interpreterStack);
        } catch (IPPException $e) {
            throw $e;
        }
        return $instruction->getParseInstruction();
    }

    public function call(string $label): void
    {

        try {
            $instructions = new XmlInterpreter($this->xmlDocument);
            $found = false;

            while ($instruction = $instructions->getParseInstruction()) {
                if ($instruction['opcode'] === 'LABEL' && $instruction['args']['arg1']['value'] === $label) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                throw new UndefinedLabelError();
            }
            $this->processedLabels[] = $label;
            array_push($this->interpreterStack, $instructions);
        } catch (IPPException $e) {
            throw $e;
        }
    }

    public function return(): void
    {
        // Проверяем, что в стеке есть хотя бы два элемента
        if (count($this->interpreterStack) >= 2) {
            array_pop($this->interpreterStack);
        } else {
            throw new MissingMemoryFrameError();
        }
    }

    public function jump(string $label): void
    {
        try {
            $instructions = new XmlInterpreter($this->xmlDocument);
            $found = false;

            while ($instruction = $instructions->getParseInstruction()) {
                if ($instruction['opcode'] === 'LABEL' && $instruction['args']['arg1']['value'] === $label) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                throw new UndefinedLabelError();
            }
            $this->processedLabels[] = $label;
            $this->interpreterStack[count($this->interpreterStack) - 1] = $instructions;
        } catch (IPPException $e) {
            throw $e;
        }
    }
}
