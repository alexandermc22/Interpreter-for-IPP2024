<?php

namespace IPP\Student\InstructionManager;

use Exception;
use IPP\Core\AbstractInterpreter;
use IPP\Core\Exception\NotImplementedException;
use IPP\Core\Exception\XMLException;
use IPP\Student\XmlInterpreter\XmlInterpreter;
use IPP\Core\Exception\IPPException;
use IPP\Student\Exception\UndefinedLabelError;
use IPP\Student\Exception\MissingMemoryFrameError;
use IPP\Student\Exception\NonExistingVariableError;
use IPP\Student\Exception\MissingValueError;
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
            throw new MissingMemoryFrameError();
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
            throw new MissingMemoryFrameError();
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

    // public function getValue(string $name)
    // {
    //     try {
    //         $variable = $this->getVariableByName($name);
    //         $value = $variable['value'];

    //         if ($value === null) {
    //             throw new MissingValueError();
    //         }

    //         return $value;
    //     } catch(IPPException $e)
    //         {
    //         throw $e;
    //         }
    // }

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
                else
                {
                    throw new MissingMemoryFrameError();
                }
                break;
            case 'T':
                if($this->temporaryFrameIsDefined)
                {
                    $variable = $this->temporaryFrame[$name] ?? null;
                    break;
                }
                else
                {
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
            throw new Exception('Invalid frame name format.');
        }
    }

    // public function setVariable(string $name, $new): void
    // {
    //     try
    //     {
    //         $oldVariable = $this->getVariableByName($name);

    //         if (in_array(substr($new, 0, 2), ['GF', 'LF', 'TF'])) {
    //             $newVariable = $this->getVariableByName($new);
    //             $oldVariable['value'] = $newVariable['value'];
    //             $oldVariable['type'] = $newVariable['type'];
    //         } else {
    //             // Разбиваем $new на тип и значение
    //             list($type, $value) = explode('@', $new, 2);

    //             // Обновляем значение и тип переменной
    //             $oldVariable['value'] = $value;
    //             $oldVariable['type'] = $type;
    //         }
    //         $this->updateVariable($oldVariable['name'],$oldVariable['type'],$oldVariable['value']);
    //     }
    //     catch(IPPException $e)
    //     {
    //         throw $e;
    //     }
    // }

    public function getValue(array $arg): string
    {
        try{

            if(substr($arg['value'], 0, 3) === 'GF@' || substr($arg['value'], 0, 3) === 'LF@'|| substr($arg['value'], 0, 3) === 'TF@')
            {
                $var= $this->getVariableByName($arg['value']);
                return $var['value'];
            }

            return $arg['value'];
        
        }
        catch(IPPException $e)
        {
            throw $e;
        }
    
    }

    public function getType(array $arg): string
    {
        try{

            if(substr($arg['value'], 0, 3) === 'GF@' || substr($arg['value'], 0, 3) === 'LF@'|| substr($arg['value'], 0, 3) === 'TF@')
            {
                $var= $this->getVariableByName($arg['value']);
                return $var['type'];
            }

            return $arg['type'];
        
        }
        catch(IPPException $e)
        {
            throw $e;
        }
    
    }

    public function updateVariable(string $name, string $type, $value): void
    {
        try
        {
            if(substr($value, 0, 3) === 'GF@' || substr($value, 0, 3) === 'LF@'|| substr($value, 0, 3) === 'TF@')
            {
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
                throw new Exception('Invalid frame name format.');
            }
        }
        catch(IPPException $e)
        {
            throw $e;
        }
    }

    // public function setVariable(string $name, string $type, $value): void
    // {
    //     if (substr($name, 0, 2) === 'GF') {

    //         if (array_key_exists($name, $this->globalFrame)) {
    //             throw new Exception('Cannot set variable in GF, variable already exist');
    //         }

    //         // Переменная для глобального фрейма
    //         $this->globalFrame[$name] = ['name' => $name,'type' => $type, 'value' => $value];

    //     } elseif (substr($name, 0, 2) === 'LF') {
    //         // Проверяем, что есть локальные фреймы
    //         if ($this->localFrameStackCount === 0) {
    //             throw new Exception('Cannot set variable in LF: no local frames available.');
    //         }

    //         // Добавляем переменную в локальный фрейм на вершине стека
    //         end($this->localFrames);
    //         $lastLocalFrame = &$this->localFrames[key($this->localFrames)];

    //         if (array_key_exists($name, $lastLocalFrame)) {
    //             throw new Exception('Cannot set variable in LF, variable already exist');
    //         }

    //         $lastLocalFrame[$name] = ['name' => $name,'type' => $type, 'value' => $value];

    //     } elseif (substr($name, 0, 2) === 'TF') {
    //         // Проверяем, что временный фрейм уже определен
    //         if (!$this->temporaryFrameIsDefined) {
    //             throw new Exception('Temporary frame is not defined.');
    //         }
    //         if (array_key_exists($name, $this->temporaryFrame)) {
    //             throw new Exception('Cannot set variable in TF, variable already exist');
    //         }
    //         // Добавляем переменную во временный фрейм
    //         $this->temporaryFrame[$name] = ['name' => $name,'type' => $type, 'value' => $value];
    //     } else {
    //         throw new Exception('Invalid variable name format.');
    //     }
    // }

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
