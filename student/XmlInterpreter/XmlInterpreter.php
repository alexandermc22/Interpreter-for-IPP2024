<?php

namespace IPP\Student\XmlInterpreter;

use IPP\Student\Exception\UnexpectedXMLError;
class XmlInterpreter
{
    protected \DOMDocument $xmlDocument;
    protected ?\DOMNodeList $instructionNodes;
    protected int $currentIndex;
    protected int $currentOrder=0;
    public function __construct(\DOMDocument $xmlDocument)
    {
        $this->xmlDocument = $xmlDocument;
        $this->instructionNodes = $this->xmlDocument->getElementsByTagName('instruction');
        $this->currentIndex = 0;
        $xsdPath = __DIR__ . '/ippcode24_shema.xsd';
        if (!$this->validateXmlAgainstXsd($this->xmlDocument,$xsdPath)) {
            throw new UnexpectedXMLError();
        }
    }

    protected function validateXmlAgainstXsd(\DOMDocument $xmlDocument, string $xsdPath): bool
    {
        $doc = new \DOMDocument();
        $doc->load($xsdPath);
        return $xmlDocument->schemaValidate($xsdPath);
    }

    public function getParseInstruction(): ?array
    {
                    // Check if the following instruction exists
        if ($this->currentIndex < $this->instructionNodes->length) {
            $instructionNode = $this->instructionNodes->item($this->currentIndex);

            // Set $instructionNode to DOMElement type
            if ($instructionNode instanceof \DOMElement) {
                $this->currentIndex++;
                $order = $instructionNode->getAttribute('order');

                if ($order<= $this->currentOrder)
                {
                    throw new UnexpectedXMLError(); 
                }
                $this->currentOrder = $order;
                $opcode = strtoupper($instructionNode->getAttribute('opcode'));
                $args = [];
                
                $expectedArg = 1;
                for ($i = 1; $i <= 3; $i++) {
                    $argNode = $instructionNode->getElementsByTagName("arg$i")->item(0);
                    if ($argNode instanceof \DOMElement) {

                        if ($i !== $expectedArg) {
                            throw new UnexpectedXMLError(); 
                        }

                        $argType = $argNode->getAttribute('type');
                        $argValue = trim($argNode->nodeValue);
                        $args["arg$i"] = ['type' => $argType, 'value' => $argValue];
                        $expectedArg++;
                    }
                }
    
                return ['order' => $order, 'opcode' => $opcode, 'args' => $args];
            }
        }
        return null; // All instructions passed or incorrect node format
    }
}
