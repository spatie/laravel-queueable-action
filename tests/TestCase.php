<?php

namespace Spatie\Skeleton\Tests;

use Illuminate\Support\Facades\Queue;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Spatie\QueueableAction\ActionJob;
use Spatie\QueueableAction\Tests\Extra\SimpleAction;

class TestCase extends OrchestraTestCase
{
    /** @test */
    public function an_action_can_be_queued()
    {
        Queue::fake();

        $action = new SimpleAction();

        $action->onQueue()->execute();

        Queue::assertPushed(ActionJob::class);
    }
}
