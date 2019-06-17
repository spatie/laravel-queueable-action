<?php

namespace Spatie\QueueableAction\Commands\Tests;

use Illuminate\Support\Facades\File;
use Spatie\QueueableAction\Tests\TestCase;

class ActionMakeCommandTest extends TestCase
{
    /** @test */
    public function it_can_create_an_action()
    {
        $actionName = 'RegisterAction';

        $filePath = $this->app['path'].'/Actions/'.$actionName.'.php';

        $pendingCommand = $this->artisan('make:action', [
            'name' => $actionName,
        ]);

        $pendingCommand->expectsOutput('Action created successfully.');

        $this->assertFileExists($filePath);

        $contents = File::get($filePath);

        $this->assertStringContainsString('namespace App\Actions;', $contents);

        $this->assertStringContainsString('class '.$actionName, $contents);

        File::delete($filePath);
    }
}
