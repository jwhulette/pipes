<?php

declare(strict_types=1);

namespace Tests\Unit\Extractors;

use Generator;
use Tests\TestCase;
use jwhulette\pipes\Extractors\XmlExtractor;

class XmlExtractorTest extends TestCase
{
    protected string $xml;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testXmlExtractZipped()
    {
        $xml = new XmlExtractor($this->xmlExtract, 'item');
        $xml->setIsZipped();
        $frameData = $xml->extract();
        $frame = $frameData->current();
        $expected = [
            'firstName' => 'Bob',
            'lastName'  => 'Smith',
            'dob'       => '02/11/1969',
        ];

        $this->assertEquals($expected, $frame->data->toArray());
    }

    public function testXmlExtract()
    {
        $xml = new XmlExtractor($this->xmlExtract, 'item');
        $frameData = $xml->extract();
        $frame = $frameData->current();
        $expected = [
            'firstName' => 'Bob',
            'lastName'  => 'Smith',
            'dob'       => '02/11/1969',
        ];

        $this->assertEquals($expected, $frame->data->toArray());
    }
}
