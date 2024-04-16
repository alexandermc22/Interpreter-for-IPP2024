<?php

namespace IPP\Student\InstructionManager;

use IPP\Core\Exception\IPPException;
use IPP\Student\Exception\UndefinedLabelError;
use IPP\Student\Exception\MissingMemoryFrameError;
use IPP\Student\Exception\NonExistingVariableError;
use IPP\Student\Exception\OperandTypeError;

class FrameManager
{
    /** @var array<int|string, array{name: string, type: string|null, value: mixed}> */
    protected array $localFrames = [];
    protected int $localFrameStackCount = 0;
    /** @var array<string, array{name: string, type: string|null, value: mixed}> */
    protected array $globalFrame = [];
    /** @var array<string, array{name: string, type: string|null, value: mixed}> */
    protected array $temporaryFrame = [];
    protected bool $temporaryFrameIsDefined = false;

    public function createFrame(): void
    {
        $this->temporaryFrameIsDefined = true;
        $this->temporaryFrame = [];
    }

    public function pushFrame(): void
    {
        $temporaryFrameReplaced = [];
        if (!$this->temporaryFrameIsDefined) {
            throw new MissingMemoryFrameError();
        }

        // Convert the temporaryFrame keys from T to L
        foreach ($this->temporaryFrame as $name => $variable) {
            $newName = 'L' . substr($name, 1);
            $temporaryFrameReplaced[$newName] = $variable;
        }

        array_push($this->localFrames, $temporaryFrameReplaced);
        $this->localFrameStackCount++;
        $this->temporaryFrameIsDefined = false;
        $this->temporaryFrame = [];
    }

    public function popFrame(): void
    {
        if ($this->localFrameStackCount === 0) {
            throw new MissingMemoryFrameError();
        }
        $localFrameReplaced = [];
        $lastLocalFrame = array_pop($this->localFrames);

        // Convert the localFrame keys from L to T
        foreach ($lastLocalFrame as $name => $variable) {
            $newName = 'T' . substr($name, 1);
            $localFrameReplaced[$newName] = $variable;
        }

        $this->temporaryFrame = $localFrameReplaced;
        $this->temporaryFrameIsDefined = true;
        $this->localFrameStackCount--;
    }

    /**
     * @return mixed
     * @throws IPPException
     */
    public function getVariableByName(string $name)
    {
        $variable = null;

        switch ($name[0]) {
            case 'G':
                $variable = $this->globalFrame[$name] ?? null;
                break;
            case 'L':
                if ($this->localFrameStackCount > 0) {
                    $variable = $this->localFrames[count($this->localFrames) - 1][$name] ?? null;
                } else {
                    throw new MissingMemoryFrameError();
                }
                break;
            case 'T':
                if ($this->temporaryFrameIsDefined) {
                    $variable = $this->temporaryFrame[$name] ?? null;
                    break;
                } else {
                    throw new MissingMemoryFrameError();
                }
        }

        if ($variable === null) {
            throw new NonExistingVariableError();
        }
        return $variable;
    }

    public function initializeVariable(string $name, string $type): void
    {
        if (substr($name, 0, 3) === 'GF@') {
            if (array_key_exists($name, $this->globalFrame)) {
                throw new UndefinedLabelError();
            }
            $this->globalFrame[$name] = ['name' => $name, 'type' => $type, 'value' => null];
        } elseif (substr($name, 0, 3) === 'LF@') {
            if ($this->localFrameStackCount === 0) {
                throw new MissingMemoryFrameError();
            }
            end($this->localFrames);
            $lastLocalFrame = &$this->localFrames[key($this->localFrames)];
            if (array_key_exists($name, $lastLocalFrame)) {
                throw new UndefinedLabelError();
            }
            $lastLocalFrame[$name] = ['name' => $name, 'type' => $type, 'value' => null];
        } elseif (substr($name, 0, 3) === 'TF@') {
            if (!$this->temporaryFrameIsDefined) {
                throw new MissingMemoryFrameError();
            }
            if (array_key_exists($name, $this->temporaryFrame)) {
                throw new UndefinedLabelError();
            }
            $this->temporaryFrame[$name] = ['name' => $name, 'type' => $type, 'value' => null];
        } else {
            throw new OperandTypeError();
        }
    }

    /**
     * @param array{name: string, type: string, value: mixed} $arg
     * @return mixed
     * @throws IPPException
     */
    public function getValue(array $arg)
    {
        try {
            if (substr($arg['value'], 0, 3) === 'GF@' || substr($arg['value'], 0, 3) === 'LF@' || substr($arg['value'], 0, 3) === 'TF@') {
                $var = $this->getVariableByName($arg['value']);
                return $var['value'];
            }

            return $arg['value'];
        } catch (IPPException $e) {
            throw $e;
        }
    }

    /**
     * @param array{name: string, type: string, value: mixed} $arg
     * @return string
     * @throws IPPException
     */
    public function getType(array $arg): string
    {
        try {
            if (substr($arg['value'], 0, 3) === 'GF@' || substr($arg['value'], 0, 3) === 'LF@' || substr($arg['value'], 0, 3) === 'TF@') {
                $var = $this->getVariableByName($arg['value']);
                return $var['type'];
            }

            return $arg['type'];
        } catch (IPPException $e) {
            throw $e;
        }
    }

    /**
     * @param string $name
     * @param string $type
     * @param mixed $value
     * @throws IPPException
     */
    public function updateVariable(string $name, string $type, $value): void
    {
        try {
            if (substr($value, 0, 3) === 'GF@' || substr($value, 0, 3) === 'LF@' || substr($value, 0, 3) === 'TF@') {
                $var = $this->getVariableByName($value);
                $value = $var['value'];
            }

            if (substr($name, 0, 3) === 'GF@') {
                $this->globalFrame[$name] = ['name' => $name, 'type' => $type, 'value' => $value];
            } elseif (substr($name, 0, 3) === 'LF@') {
                if ($this->localFrameStackCount === 0) {
                    throw new MissingMemoryFrameError();
                }
                end($this->localFrames);
                $lastLocalFrame = &$this->localFrames[key($this->localFrames)];
                $lastLocalFrame[$name] = ['name' => $name, 'type' => $type, 'value' => $value];
            } elseif (substr($name, 0, 3) === 'TF@') {
                if (!$this->temporaryFrameIsDefined) {
                    throw new MissingMemoryFrameError();
                }
                $this->temporaryFrame[$name] = ['name' => $name, 'type' => $type, 'value' => $value];
            } else {
                throw new OperandTypeError();
            }
        } catch (IPPException $e) {
            throw $e;
        }
    }

    /**
     * @return array<string, array{name: string, type: string|null, value: mixed}>
     */
    public function getGlobalFrame(): array
    {
        return $this->globalFrame;
    }

    /**
     * @return array<array{name: string, type: string|null, value: mixed}>
     */
    public function getLocalFrames(): array
    {
        return $this->localFrames;
    }

    /**
     * @return array<string, array{name: string, type: string|null, value: mixed}>
     */
    public function getTemporaryFrame(): array
    {
        return $this->temporaryFrame;
    }
}
