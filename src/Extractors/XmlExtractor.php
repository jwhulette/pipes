<?php

declare(strict_types=1);

namespace jwhulette\pipes\Extractors;

use Generator;
use XMLReader;
use DOMDocument;
use jwhulette\pipes\Frame;

class XmlExtractor
{
    /** @var string */
    protected $file;

    /** @var string */
    protected $nodename;

    /** @var \jwhulette\pipes\Frame */
    protected $frame;

    /**
     * XmlExtractor.
     *
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
     * Extract the data from the source file.
     *
     * @return Generator
     */
    public function extract(): Generator
    {
        $reader = new XMLReader();
        $reader->open($this->file);

        while ($reader->read()) {
            if ($reader->nodeType == XMLReader::ELEMENT and $reader->name === $this->nodename) {
                $doc = new DOMDocument('1.0', 'UTF-8');
                // Create simple xml object for convinient access to subelements
                $record = (array) simplexml_import_dom($doc->importNode($reader->expand(), true));
                yield $this->frame->setData($record);
            }
        }

        $this->end();
        yield $this->frame;

        $reader->close();
    }

    /**
     * Set the extractor end flag.
     */
    public function end(): void
    {
        $this->frame->setEnd();
    }
}
