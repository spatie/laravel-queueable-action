<?php

namespace Spatie\QueueableAction\Tests\Testing;

use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;
use Spatie\QueueableAction\Testing\QueueableActionFake;
use Spatie\QueueableAction\Tests\TestCase;
use Spatie\QueueableAction\Tests\TestClasses\SimpleAction;

class QueueableActionTest extends TestCase
{
    /** @test */
    public function it_can_assert_an_action_was_pushed()
    {
        Queue::fake();

        $action = new SimpleAction();

        $action->onQueue()->execute();

        QueueableActionFake::assertPushed(SimpleAction::class);
    }

    /** @test */
    public function it_can_assert_an_action_was_pushed_times()
    {
        Queue::fake();

        $action = new SimpleAction();

        $action->onQueue()->execute();
        $action->onQueue()->execute();

        QueueableActionFake::assertPushedTimes(SimpleAction::class, 2);
    }

    /** @test */
    public function it_can_assert_an_action_was_not_pushed()
    {
        Queue::fake();

        QueueableActionFake::assertNotPushed(SimpleAction::class);
    }

    /** @test */
    public function it_nags_the_queue_is_not_fake()
    {
        try {
            QueueableActionFake::assertNotPushed(SimpleAction::class);
        } catch (ExpectationFailedException $exception) {
            $this->assertStringContainsString('Queue was not faked. Use `Queue::fake()`', $exception->toString());

            return;
        }

        Assert::fail('QueueableAction did not complain about missing `Queue::fake()`.');
    }
}
