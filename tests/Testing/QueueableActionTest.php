<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Assert;
use function PHPUnit\Framework\assertStringContainsString;
use PHPUnit\Framework\ExpectationFailedException;
use Spatie\QueueableAction\ActionJob;
use Spatie\QueueableAction\Testing\QueueableActionFake;
use Spatie\QueueableAction\Tests\TestClasses\CustomActionJob;
use Spatie\QueueableAction\Tests\TestClasses\QueueableActionFakeTestClass;

use Spatie\QueueableAction\Tests\TestClasses\SimpleAction;

it('can assert an action was pushed', function () {
    Queue::fake();

    $action = new SimpleAction();

    $action->onQueue()->execute();

    QueueableActionFake::assertPushed(SimpleAction::class);
});

it('can assert an action was pushed times', function () {
    Queue::fake();

    $action = new SimpleAction();

    $action->onQueue()->execute();
    $action->onQueue()->execute();

    QueueableActionFake::assertPushedTimes(SimpleAction::class, 2);
});

it('can assert an action was not pushed', function () {
    Queue::fake();

    QueueableActionFake::assertNotPushed(SimpleAction::class);
});

it('nags the queue is not fake', function () {
    try {
        QueueableActionFake::assertNotPushed(SimpleAction::class);
    } catch (ExpectationFailedException $exception) {
        assertStringContainsString('Queue was not faked. Use `Queue::fake()`', $exception->toString());

        return;
    }

    Assert::fail('QueueableAction did not complain about missing `Queue::fake()`.');
});

it('can assert an action with chain was pushed', function () {
    Queue::fake();

    $action = new SimpleAction();

    $action->onQueue()
        ->execute()
        ->chain([
            new ActionJob(SimpleAction::class),
            new ActionJob(SimpleAction::class),
        ]);

    QueueableActionFake::assertPushedWithChain(SimpleAction::class, [SimpleAction::class, SimpleAction::class]);
});

it('can assert an action without chain was pushed', function () {
    Queue::fake();

    $action = new SimpleAction();

    $action->onQueue()->execute();

    QueueableActionFake::assertPushedWithoutChain(SimpleAction::class);
});

test('get pushed count can use custom action job class', function () {
    Config::set('queuableaction.job_class', CustomActionJob::class);
    Queue::fake();

    $action = new SimpleAction();
    $action->onQueue()->execute();

    expect(
        QueueableActionFakeTestClass::getPushedCountTest(SimpleAction::class)
    )->toEqual(1);
});

test('get chained classes can use custom action job class', function () {
    Config::set('queuableaction.job_class', CustomActionJob::class);
    Queue::fake();

    $action = new SimpleAction();

    $action->onQueue()
        ->execute()
        ->chain([
            new CustomActionJob(SimpleAction::class),
            new CustomActionJob(SimpleAction::class),
        ]);

    expect(QueueableActionFakeTestClass::getChainedClassesTest())->toHaveCount(2);
});
