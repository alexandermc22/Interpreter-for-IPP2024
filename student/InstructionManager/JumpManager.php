<?php

namespace IPP\Student\InstructionManager;

use IPP\Student\Exception\UnexpectedXMLError;
use Exception;
use IPP\Student\XmlInterpreter\XmlInterpreter;
use IPP\Core\Exception\IPPException;
use IPP\Student\Exception\MissingMemoryFrameError;
use IPP\Student\Exception\UndefinedLabelError;

class JumpManager
{
    protected array $interpreterStack = [];
    protected \DOMDocument $xmlDocument;
    protected array $processedLabels = [];

    /**
     * Constructor for JumpManager.
     * Initializes the JumpManager with the given XML document.
     *
     * @param \DOMDocument $xmlDocument The XML document to initialize JumpManager with.
     * @throws UnexpectedXMLError If an unexpected XML error occurs.
     */
    public function __construct(\DOMDocument $xmlDocument)
    {
        try {
            $this->xmlDocument = $xmlDocument;
            $instructions = new XmlInterpreter($xmlDocument);
            $this->interpreterStack[] = $instructions;
        } catch (Exception) {
            throw new UnexpectedXMLError();
        }
    }

    /**
     * Check if the given label exists in the processed labels.
     *
     * @param string $label The label to check for existence.
     * @return bool True if the label exists, false otherwise.
     */
    public function isExist(string $label): bool
    {
        return in_array($label, $this->processedLabels);
    }

    /**
     * Get the next instruction from the interpreter stack.
     *
     * @return array|null The next instruction array or null if stack is empty.
     * @throws IPPException If an IPP exception occurs during retrieval.
     */
    public function getNextInstruction(): ?array
    {
        try {
            $instruction = end($this->interpreterStack);
        } catch (IPPException $e) {
            throw $e;
        }
        return $instruction->getParseInstruction();
    }

    /**
     * Call the instruction with the given label.
     * Adds the corresponding instructions to the interpreter stack.
     *
     * @param string $label The label to call.
     * @throws IPPException If an IPP exception occurs during call.
     */
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

    /**
     * Return from the current context.
     * Pops the top interpreter from the stack.
     *
     * @throws MissingMemoryFrameError If there are not enough frames to return from.
     */
    public function return(): void
    {
        if (count($this->interpreterStack) >= 2) {
            array_pop($this->interpreterStack);
        } else {
            throw new MissingMemoryFrameError();
        }
    }

    /**
     * Jump to the instruction with the given label.
     * Updates the interpreter stack with the corresponding instructions.
     *
     * @param string $label The label to jump to.
     * @throws IPPException If an IPP exception occurs during jump.
     */
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
