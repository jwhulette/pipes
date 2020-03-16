<?php

declare(strict_types=1);

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected string $testDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(getcwd().'/tests/database');

        $this->testFilesDirectory = getcwd().'/tests/files';
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
