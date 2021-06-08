<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Extractors;

use Generator;
use XMLReader;
use SimpleXMLElement;
use Jwhulette\Pipes\Frame;

class XmlExtractor extends Extractor implements ExtractorInterface
{
    protected string $nodename;

    protected bool $isGZipped = false;

    /**
     * @param string $file
     * @param string $nodename
     */
    public function __construct(string $file, string $nodename)
    {
        $this->file = $file;

        $this->nodename = $nodename;

        $this->frame = new Frame();
    }

    /**
     * @return  XmlExtractor
     */
    public function setIsGZipped(): XmlExtractor
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
     * Flatten the multidimentional array.
     *
     * @param array $array
     *
     * @return array
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
     * @param array $record
     *
     * @return array
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
