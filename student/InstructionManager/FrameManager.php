<?php

namespace IPP\Student\InstructionManager;

use Exception;
use IPP\Core\AbstractInterpreter;
use IPP\Core\Exception\NotImplementedException;
use IPP\Core\Exception\XMLException;
use IPP\Student\XmlInterpreter\XmlInterpreter;
use IPP\Core\Exception\IPPException;
class FrameManager
{
    protected array $localFrames = [];
    protected int $localFrameStackCount = 0;
    protected array $globalFrame = [];
    protected array $temporaryFrame = [];
    protected bool $temporaryFrameIsDefined = false;
    
    public function createFrame(): void
    {
        $this->temporaryFrameIsDefined = true;
        $this->temporaryFrame = [];
    }


    public function pushFrame(): void
    {
        if (!$this->temporaryFrameIsDefined) {
            throw new \Exception('Temporary frame is not defined.');
        }

        foreach ($this->temporaryFrame as $name => $variable) {
                $newName = 'L' . substr($name, 1); // Заменяем "T" на "L" в имени переменной
                $temporaryFrameReplaced[$newName] = $variable; // Добавляем переменную с новым именем
        }

        // Добавляем temporaryFrame на вершину стека localFrames
        array_push($this->localFrames, $temporaryFrameReplaced);
        $this->localFrameStackCount++;
        // Сбрасываем temporaryFrameIsDefined в false и очищаем temporaryFrame
        $this->temporaryFrameIsDefined = false;
        $this->temporaryFrame = [];
    }


    public function popFrame(): void
    {
        if ($this->localFrameStackCount === 0) {
            throw new \Exception('No local frames to pop.');
        }

        // Удаляем элемент из вершины стека localFrames и добавляем его в temporaryFrame
        $lastLocalFrame = array_pop($this->localFrames);

        foreach ( $lastLocalFrame as $name => $variable) {
            $newName = 'T' . substr($name, 1); // Заменяем "T" на "L" в имени переменной
            $localFrameReplaced[$newName] = $variable; // Добавляем переменную с новым именем
        }

        $this->temporaryFrame = $localFrameReplaced;
        $this->temporaryFrameIsDefined = true;

        // Уменьшаем счетчик стека локальных фреймов
        $this->localFrameStackCount--;
    }

    public function getVariableByName(string $name)
    {
        $variable = null;

        switch ($name[0]) {
            case 'G':
                $variable = $this->globalFrame[$name] ?? null;
                break;
            case 'L':
                // Ищем переменную только на вершине стека локальных фреймов
                if ($this->localFrameStackCount > 0) {
                    $variable = $this->localFrames[count($this->localFrames) - 1][$name] ?? null;
                }
                break;
            case 'T':
                $variable = $this->temporaryFrame[$name] ?? null;
                break;
        }

        if ($variable === null) {
            throw new \Exception('Variable not found: ' . $name);
        }
        return $variable;
    }


    public function setVariable(string $name, string $type, $value): void
    {
        if (substr($name, 0, 2) === 'GF') {

            if (array_key_exists($name, $this->globalFrame)) {
                throw new Exception('Cannot set variable in GF, variable already exist');
            }

            // Переменная для глобального фрейма
            $this->globalFrame[$name] = ['name' => $name,'type' => $type, 'value' => $value];

        } elseif (substr($name, 0, 2) === 'LF') {
            // Проверяем, что есть локальные фреймы
            if ($this->localFrameStackCount === 0) {
                throw new Exception('Cannot set variable in LF: no local frames available.');
            }

            // Добавляем переменную в локальный фрейм на вершине стека
            end($this->localFrames);
            $lastLocalFrame = &$this->localFrames[key($this->localFrames)];

            if (array_key_exists($name, $lastLocalFrame)) {
                throw new Exception('Cannot set variable in LF, variable already exist');
            }

            $lastLocalFrame[$name] = ['name' => $name,'type' => $type, 'value' => $value];

        } elseif (substr($name, 0, 2) === 'TF') {
            // Проверяем, что временный фрейм уже определен
            if (!$this->temporaryFrameIsDefined) {
                throw new Exception('Temporary frame is not defined.');
            }
            if (array_key_exists($name, $this->temporaryFrame)) {
                throw new Exception('Cannot set variable in TF, variable already exist');
            }
            // Добавляем переменную во временный фрейм
            $this->temporaryFrame[$name] = ['name' => $name,'type' => $type, 'value' => $value];
        } else {
            throw new Exception('Invalid variable name format.');
        }
    }

    public function getGlobalFrame(): array
    {
        return $this->globalFrame;
    }

    public function getLocalFrames(): array
    {
        return $this->localFrames;
    }

    public function getTemporaryFrame(): array
    {
        return $this->temporaryFrame;
    }
}
