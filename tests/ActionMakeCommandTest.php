<?php

use Illuminate\Filesystem\Filesystem;
use Mockery\MockInterface;

use function Pest\Laravel\artisan;
use function Pest\Laravel\mock;

function expectsGeneratedClass(string $filename, string $contents): void
{
    mock(Filesystem::class, static function (MockInterface $mock) use ($filename, $contents) {
        $mock->makePartial()
            ->expects('put')
            ->withArgs(static function ($path, $compiled) use ($filename, $contents) {
                return $path === $filename
                    && $compiled === $contents;
            })
            ->andReturn(true);
    });
}

it('generates queueable actions', function () {
    expectsGeneratedClass(
        app_path('Actions/TestAction.php'),
        file_get_contents(__DIR__ . '/stubs/test-action-queued.stub')
    );

    artisan('make:action', [
        'name' => 'TestAction',
    ])->expectsOutputToContain('Action [app/Actions/TestAction.php] created successfully.')->assertExitCode(0);
});

it('generates synchronous actions', function () {
    expectsGeneratedClass(
        app_path('Actions/TestAction.php'),
        file_get_contents(__DIR__ . '/stubs/test-action.stub')
    );

    artisan('make:action', [
        'name' => 'TestAction',
        '--sync' => true,
    ])->expectsOutputToContain('Action [app/Actions/TestAction.php] created successfully.')->assertExitCode(0);
});
