<?php

namespace Spatie\QueueableAction\Tests;

use Spatie\QueueableAction\ActionJob;
use Spatie\QueueableAction\Tests\TestClasses\DataObject;
use Spatie\QueueableAction\Tests\TestClasses\SimpleAction;
use Spatie\QueueableAction\Tests\TestClasses\ComplexAction;

class ActionJobTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated_from_the_action_class()
    {
        $actionJob = new ActionJob(SimpleAction::class);

        $this->assertInstanceOf(ActionJob::class, $actionJob);
        $this->assertEquals(SimpleAction::class, $actionJob->displayName());
    }

    /** @test */
    public function it_can_be_instantiated_from_an_action_instance()
    {
        $complexAction = app(ComplexAction::class);

        $actionJob = new ActionJob($complexAction, [new DataObject('foo')]);

        $this->assertInstanceOf(ActionJob::class, $actionJob);
        $this->assertEquals(ComplexAction::class, $actionJob->displayName());
    }
}
