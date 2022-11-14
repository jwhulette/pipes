<?php

declare(strict_types=1);

namespace jwhulette\pipes\Extractors;

use Generator;
use jwhulette\pipes\Frame;
use SimpleXMLElement;
use XMLReader;

class XmlExtractor implements ExtractorInterface
{
    protected string $file;

    protected string $nodeName;

    protected Frame $frame;

    protected bool $isGZipped = false;

    public function __construct(string $file, string $nodeName)
    {
        $this->file = $file;

        $this->nodeName = $nodeName;

        $this->frame = new Frame();
    }

    /**
     * @return  XmlExtractor
     */
    public function setIsGZipped(): self
    {
        $this->isGZipped = true;

        return $this;
    }

    /**
     * @return Generator
     */
    public function extract(): Generator
    {
        $reader = new XMLReader();
        if ($this->isGZipped) {
            $reader->open('compress.zlib://' . $this->file);
        } else {
            $reader->open($this->file);
        }

        while ($reader->read()) {
            if ($reader->nodeType == XMLReader::ELEMENT and $reader->name === $this->nodeName) {
                $element = new SimpleXMLElement($reader->readOuterXML());

                $xmlRecord = $this->loopXml($element);

                $record = $this->arrayFlatten($xmlRecord);

                yield $this->frame->setData($record);
            }
        }

        $this->frame->setEnd();

        $reader->close();
    }

    /**
     * Flatten the multidimensional array.
     *
     * @param array<int|string,int|string|array<int,string>> $array
     *
     * @return array<int|string,int|string>
     */
    private function arrayFlatten(array $array): array
    {
        $return = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $return = array_merge($return, $this->arrayFlatten($value));
            } else {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    /**
     * Get all the xml nodes as an array.
     *
     * @param SimpleXMLElement $element
     * @param array<string,string> $record
     *
     * @return array<string,string>
     */
    private function loopXml(SimpleXMLElement $element, $record = []): array
    {
        foreach ($element->children() as $node) {
            if ($node->count() > 0) {
                $record[$node->getName()][] = $this->loopXml($node);
            } else {
                $record[$node->getName()] = (string) $node;
            }
        }

        return $record;
    }
}
