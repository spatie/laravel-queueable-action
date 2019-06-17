<?php

namespace Spatie\QueueableAction\Console\Tests;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Spatie\QueueableAction\Tests\TestCase;

class ActionMakeCommandTest extends TestCase
{
    /** @test */
    public function it_can_create_an_action()
    {
        $exitCode = Artisan::call('make:action', [
            'name' => 'SimpleAction',
        ]);

        $this->assertEquals(0, $exitCode);

        $this->assertContains('Action created successfully.', Artisan::output());

        $shouldOutputFilePath = $this->app['path'].'/Actions/SimpleAction.php';

        $this->assertTrue(File::exists($shouldOutputFilePath), 'File exists in default app/Actions folder');

        $contents = File::get($shouldOutputFilePath);

        $this->assertContains('namespace App\Actions;', $contents);

        $this->assertContains('class SimpleAction', $contents);

        dump(App\Actions\SimpleAction::class);
    }
}
