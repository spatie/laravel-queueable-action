<?php

namespace Spatie\QueueableAction\Tests;

use Illuminate\Filesystem\Filesystem;
use Mockery\MockInterface;

class ActionMakeCommandTest extends TestCase
{
    /** @test */
    public function it_generates_queueable_actions(): void
    {
        $this->expectsGeneratedClass('TestAction', file_get_contents(__DIR__ . '/stubs/test-action-queued.stub'));

        $this->artisan('make:action', [
            'name' => 'TestAction'
        ])->expectsOutput('Action created successfully.')->assertExitCode(0);
    }

    /** @test */
    public function it_generates_synchronous_actions(): void
    {
        $this->expectsGeneratedClass('TestAction', file_get_contents(__DIR__ . '/stubs/test-action.stub'));

        $this->artisan('make:action', [
            'name' => 'TestAction',
            '--sync' => true
        ])->expectsOutput('Action created successfully.')->assertExitCode(0);
    }

    private function expectsGeneratedClass(string $filename, string $contents): void
    {
        $this->mock(Filesystem::class, static function (MockInterface $mock) use ($filename, $contents) {
            $mock->makePartial()
                ->expects('put')
                ->withArgs(static function ($path, $compiled) use ($filename, $contents) {
                    return $path === app_path("Actions/{$filename}.php")
                        && $compiled === $contents;
                })
                ->andReturn(true);
        });
    }
}
