<?php

namespace Spatie\QueueableAction\Tests;

use Illuminate\Support\Facades\Queue;
use Spatie\QueueableAction\ActionJob;
use Spatie\QueueableAction\Tests\TestClasses\DataObject;
use Spatie\QueueableAction\Tests\TestClasses\SimpleAction;
use Spatie\QueueableAction\Tests\TestClasses\ComplexAction;

class QueueableActionTest extends TestCase
{
    /** @test */
    public function an_action_can_be_queued()
    {
        Queue::fake();

        $action = new SimpleAction();

        $action->onQueue()->execute();

        Queue::assertPushed(ActionJob::class);
    }

    /** @test */
    public function an_action_with_dependencies_and_input_can_be_executed_on_the_queue()
    {
        /** @var \Spatie\QueueableAction\Tests\TestClasses\ComplexAction $action */
        $action = app(ComplexAction::class);

        $action->onQueue()->execute(new DataObject('foo'));

        $this->assertLogHas('foo bar');
    }

    /** @test */
    public function an_action_can_be_executed_on_a_queue()
    {
        Queue::fake();

        /** @var \Spatie\QueueableAction\Tests\TestClasses\ComplexAction $action */
        $action = app(ComplexAction::class);

        $action->queue = 'other';

        $action->onQueue()->execute(new DataObject('foo'));

        Queue::assertPushedOn('other', ActionJob::class);
    }

    /** @test */
    public function an_action_can_be_executed_on_a_queue_using_the_on_queue_method()
    {
        Queue::fake();

        /** @var \Spatie\QueueableAction\Tests\TestClasses\ComplexAction $action */
        $action = app(ComplexAction::class);

        $action->onQueue('other')->execute(new DataObject('foo'));

        Queue::assertPushedOn('other', ActionJob::class);
    }

    /** @test */
    public function an_action_is_executed_immediately_if_not_queued()
    {
        Queue::fake();

        /** @var \Spatie\QueueableAction\Tests\TestClasses\ComplexAction $action */
        $action = app(ComplexAction::class);

        $action->queue = 'other';

        $action->execute(new DataObject('foo'));

        Queue::assertNotPushed(ActionJob::class);

        $this->assertLogHas('foo bar');
    }

    /** @test */
    public function an_action_can_be_queued_with_a_chain_of_other_actions_jobs()
    {
        Queue::fake();

        /** @var \Spatie\QueueableAction\Tests\TestClasses\ComplexAction $action */
        $action = app(ComplexAction::class);

        $action->onQueue()
            ->execute(new DataObject('foo'))
            ->chain([
                new ActionJob(SimpleAction::class),
            ]);

        Queue::assertPushedWithChain(ActionJob::class, [
            new ActionJob(SimpleAction::class),
        ]);
    }
}
