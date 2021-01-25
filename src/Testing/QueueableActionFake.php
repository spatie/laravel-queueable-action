<?php

namespace Spatie\QueueableAction\Testing;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Testing\Fakes\QueueFake;
use PHPUnit\Framework\Assert;
use Spatie\QueueableAction\ActionJob;

class QueueableActionFake
{
    public static function assertPushed(string $actionJobClass)
    {
        static::assertQueueIsFake();

        $pushed = static::actionJobWasPushed($actionJobClass);

        Assert::assertTrue($pushed, "`{$actionJobClass}` was not pushed.");
    }

    public static function assertPushedTimes(string $actionJobClass, int $times = 1)
    {
        static::assertQueueIsFake();

        $pushedCount = static::getPushedCount($actionJobClass);

        Assert::assertTrue($pushedCount === $times, "`{$actionJobClass}` was pushed {$pushedCount} times. Expected {$times} times.");
    }

    public static function assertNotPushed(string $actionJobClass)
    {
        static::assertQueueIsFake();

        $pushed = static::actionJobWasPushed($actionJobClass);

        Assert::assertFalse($pushed, "`{$actionJobClass}` was pushed.");
    }

    public static function assertPushedWithChain(string $actionJobClass, array $expectedActionChain = [])
    {
        static::assertQueueIsFake();

        $pushed = static::actionJobWasPushed($actionJobClass);

        Assert::assertTrue($pushed, "`{$actionJobClass}` was not pushed.");

        Assert::assertTrue(
            static::getChainedClasses()->all() === $expectedActionChain,
            'The expected chain was not pushed.'
        );
    }

    public static function assertPushedWithoutChain(string $actionJobClass)
    {
        static::assertQueueIsFake();

        $pushed = static::actionJobWasPushed($actionJobClass);

        Assert::assertTrue($pushed, "`{$actionJobClass}` was not pushed.");

        $matching = static::getChainedClasses();

        Assert::assertTrue(
            $matching->isEmpty(),
            'The expected chain was not empty.'
        );
    }

    protected static function actionJobWasPushed(string $actionJobClass): bool
    {
        return static::getPushedCount($actionJobClass) > 0;
    }

    protected static function getPushedCount(string $actionJobClass): int
    {
        return collect(Queue::pushedJobs()[ActionJob::class] ?? [])
            ->map(function (array $queuedJob) {
                return $queuedJob['job']->displayName();
            })
            ->filter(function (string $displayName) use ($actionJobClass) {
                return $displayName === $actionJobClass;
            })
            ->count();
    }

    protected static function assertQueueIsFake()
    {
        Assert::assertTrue(Queue::getFacadeRoot() instanceof QueueFake, 'Queue was not faked. Use `Queue::fake()`.');
    }

    protected static function getChainedClasses()
    {
        return collect(Queue::pushedJobs()[ActionJob::class] ?? [])
            ->map(fn ($actionJob) => $actionJob['job']->chained)
            ->map(function ($chain) {
                return collect($chain)->map(function ($job) {
                    return unserialize($job)->displayName();
                });
            })
            ->flatten();
    }
}
