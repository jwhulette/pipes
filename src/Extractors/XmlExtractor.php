<?php

declare(strict_types=1);

namespace jwhulette\pipes\Extractors;

use Generator;
use XMLReader;
use SimpleXMLElement;
use jwhulette\pipes\Frame;

class XmlExtractor implements ExtractorInterface
{
    protected string $file;
    protected string $nodename;
    protected Frame $frame;
    protected bool $isZipped;

    /**
     * XmlExtractor.
     *
     * @param string $file
     * @param string $nodename
     * @param bool $isZipped
     */
    public function __construct(string $file, string $nodename, bool $isZipped = false)
    {
        $this->file = $file;
        $this->nodename = $nodename;
        $this->isZipped = $isZipped;
        $this->frame = new Frame();
    }

    /**
     * Extract the data from the source file.
     *
     * @return Generator
     */
    public function extract(): Generator
    {
        $reader = new XMLReader();
        if ($this->isZipped) {
            $reader->open('compress.zlib://'.$this->file);
        } else {
            $reader->open($this->file);
        }

        while ($reader->read()) {
            if ($reader->nodeType == XMLReader::ELEMENT and $reader->name === $this->nodename) {
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
     * Flatten the multidimentional array
     *
     * @param array $array
     *
     * @return array
     */
    private function arrayFlatten(array $array): array
    {
        $return = array();
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
     * Get all the xml nodes as an array
     *
     * @param SimpleXMLElement $element
     * @param array $record
     *
     * @return array
     */
    private function loopXml($element, $record = []): array
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
