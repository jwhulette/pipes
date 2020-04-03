<?php

declare(strict_types=1);

namespace Tests\Unit\Extractors;

use ZipArchive;
use Tests\TestCase;
use org\bovigo\vfs\vfsStream;
use Illuminate\Support\Facades\File;
use Tests\factories\DataFileFactory;
use jwhulette\pipes\Extractors\XmlExtractor;

class XmlExtractorTest extends TestCase
{
    protected string $testfile;
    protected string $testZipfile;

    public function setUp(): void
    {
        parent::setUp();

        $directory = [
            'extractor.xml',
        ];

        $this->vfs = vfsStream::setup(sys_get_temp_dir(), null, $directory);

        $this->testfile = $this->vfs->url().'/extractor.xml';

        (new DataFileFactory($this->testfile))->asXml()->create();
    }

    public function testXmlExtractZipped()
    {
        $testZipfile = sys_get_temp_dir().'/testgzip.gz';

        $fp = gzopen($testZipfile, 'w');

        gzwrite($fp, file_get_contents($this->testfile));

        gzclose($fp);

        $xml = new XmlExtractor($testZipfile, 'item');

        $xml->setIsGZipped();

        $frameData = $xml->extract();

        $frame = $frameData->current();

        $expected = [
            'firstName' => 'BOB',
            'lastName'  => 'SMITH',
            'dob'       => '02/11/1969',
        ];

        $this->assertEquals($expected, $frame->data->toArray());
    }

    public function testXmlExtract()
    {
        $xml = new XmlExtractor($this->testfile, 'item');

        $frameData = $xml->extract();

        $frame = $frameData->current();

        $expected = [
            'firstName' => 'BOB',
            'lastName'  => 'SMITH',
            'dob'       => '02/11/1969',
        ];

        $this->assertEquals($expected, $frame->data->toArray());
    }
}
