<?php

declare(strict_types=1);

namespace Tests\Unit\Extractors;

use Generator;
use Tests\TestCase;
use jwhulette\pipes\Extractors\XmlExtractor;

class XmlExtractorTest extends TestCase
{
    /** @var string */
    protected $xml;

    public function setUp(): void
    {
        parent::setUp();

    }

    public function testExtractorCsvInstance()
    {
        $xml = new XmlExtractor($this->xmlExtract, 'item');

        $this->assertInstanceOf(XmlExtractor::class, $xml);
    }

    public function testHasGenerator()
    {
        $xml = new XmlExtractor($this->xmlExtract, 'item');

        $frame = $xml->extract();

        $this->assertInstanceOf(Generator::class, $frame);
    }

   
    public function testHasData()
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
