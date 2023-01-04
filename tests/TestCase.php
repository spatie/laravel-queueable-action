<?php

namespace Spatie\QueueableAction\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Spatie\QueueableAction\QueueableActionServiceProvider;

class TestCase extends OrchestraTestCase
{
    const LOG_PATH = __DIR__ . '/temp/queue.log';

    protected function setUp(): void
    {
        parent::setUp();

        clearLog();
    }

    protected function getPackageProviders($app): array
    {
        return [
            QueueableActionServiceProvider::class,
        ];
    }
}
