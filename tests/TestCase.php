<?php

declare(strict_types=1);

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected $csvExtract = 'tests/files/test_input.csv';
    protected $csvExtractNoHeader = 'tests/files/test_input_no_header.csv';
    protected $csvLoader = 'tests/files/test_output.csv';
    protected $fixedWidthExtract = 'tests/files/test_fixed_with_input.csv';
    protected $fixedWidthExtractNoHeader = 'tests/files/test_fixed_with_input_no_header.csv';
    protected $xlsxExtract = 'tests/files/test_input.xlsx';
    protected $xlsxExtractNoHeader = 'tests/files/test_input_no_header.xlsx';
    protected $xmlExtract = 'tests/files/test_input.xml';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(getcwd().'/tests/database');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
