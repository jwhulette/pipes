<?php

declare(strict_types=1);

namespace Jwhulette\Pipes\Tests\Unit\Transformers;

use Jwhulette\Pipes\Extractors\XlsxExtractor;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Tests\database\factories\DataFileFactory;
use Tests\TestCase;

class XlsxExtractorTest extends TestCase
{
    protected string $extract;

    protected string $extractNoHeader;

    protected TemporaryDirectory $temporaryDirectory;

    public function setUp(): void
    {
        parent::setUp();

        $header = [
            'FIRSTNAME',
            'LASTNAME',
            'DOB',
            'AMOUNT',
        ];

        /*
         * Need to create a temp file as Spout Box is not able
         * to read from the vfStream file stream
         */
        $this->temporaryDirectory = (new TemporaryDirectory())->create();
        $this->extract = $this->temporaryDirectory->path('extractor.xlsx');
        $this->extractNoHeader = $this->temporaryDirectory->path('no_header_extractor.xlsx');

        (new DataFileFactory($this->extract))
            ->asXlsx()
            ->setHeader($header)
            ->create();

        (new DataFileFactory($this->extractNoHeader))
            ->asXlsx()
            ->create();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->temporaryDirectory->delete();
    }

    /** @test */
    public function it_has_header()
    {
        $frameData = (new XlsxExtractor($this->extract))
            ->extract();

        $frame = $frameData->current();

        $expected = [
            'FIRSTNAME',
            'LASTNAME',
            'DOB',
            'AMOUNT',
        ];

        $expectedData = [
            'FIRSTNAME' => 'BOB',
            'LASTNAME' => 'SMITH',
            'DOB' => '02/11/1969',
            'AMOUNT' => '$22.00',
        ];

        $this->assertEquals($expected, $frame->getHeader()->values()->toArray());

        $this->assertEquals($expectedData, $frame->getData()->toArray());
    }

    /** @test */
    public function it_has_no_header()
    {
        $frameData = (new XlsxExtractor($this->extractNoHeader))
            ->setNoHeader()
            ->extract();

        $frame = $frameData->current();

        $expected = [
            'BOB',
            'SMITH',
            '02/11/1969',
            '$22.00',
        ];

        $this->assertEquals($expected, $frame->getData()->toArray());
    }

    /** @test */
    public function it_can_set_sheet_index()
    {
        $frameData = (new XlsxExtractor($this->extract))
            ->setSheetIndex(0)
            ->extract();

        $frame = $frameData->current();

        $expected = [
            'FIRSTNAME' => 'BOB',
            'LASTNAME' => 'SMITH',
            'DOB' => '02/11/1969',
            'AMOUNT' => '$22.00',
        ];

        $this->assertEquals($expected, $frame->getData()->toArray());
    }
}
