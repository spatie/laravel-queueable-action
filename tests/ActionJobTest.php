<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Queue\WorkerOptions;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Spatie\QueueableAction\ActionJob;
use Spatie\QueueableAction\Tests\TestClasses\ComplexAction;
use Spatie\QueueableAction\Tests\TestClasses\CustomActionJob;
use Spatie\QueueableAction\Tests\TestClasses\DataObject;
use Spatie\QueueableAction\Tests\TestClasses\FailingAction;
use Spatie\QueueableAction\Tests\TestClasses\QueueablePropertiesAction;
use Spatie\QueueableAction\Tests\TestClasses\SimpleAction;

it('can be instantiated from the action class', function () {
    $actionJob = new ActionJob(SimpleAction::class);

    expect($actionJob)->toBeInstanceOf(ActionJob::class)
        ->and($actionJob->displayName())->toEqual(SimpleAction::class);
});

it('can be instantiated from an action instance', function () {
    $complexAction = app(ComplexAction::class);

    $actionJob = new ActionJob($complexAction, [new DataObject('foo')]);

    expect($actionJob)->toBeInstanceOf(ActionJob::class)
        ->and($actionJob->displayName())->toEqual(ComplexAction::class);
});

it('copies the queueable properties from the action without deprecations', function () {
    $deprecations = [];

    set_error_handler(function ($severity, $message) use (&$deprecations) {
        if ($severity !== E_DEPRECATED) {
            return false;
        }

        $deprecations[] = $message;

        return true;
    });

    try {
        $actionJob = new ActionJob(QueueablePropertiesAction::class);
    } finally {
        restore_error_handler();
    }

    expect($actionJob->tries)->toBe(5)
        ->and($actionJob->timeout)->toBe(120)
        ->and($actionJob->maxExceptions)->toBe(2)
        ->and($deprecations)->toBe([]);
});

it('preserves queue worker limits and defaults', function ($actionClass, $tries, $timeout, $maxExceptions, $workerTries, $failureAttempt, $jobClass = ActionJob::class) {
    config()->set('database.default', 'testing');
    config()->set('queue.default', 'database');
    config()->set('queuableaction.job_class', $jobClass);
    config()->set('queue.connections.database', [
        'driver' => 'database',
        'connection' => 'testing',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 60,
    ]);

    Schema::create('jobs', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('queue');
        $table->longText('payload');
        $table->unsignedTinyInteger('attempts');
        $table->unsignedInteger('reserved_at')->nullable();
        $table->unsignedInteger('available_at');
        $table->unsignedInteger('created_at');
    });

    $action = app($actionClass);

    if ($action instanceof QueueablePropertiesAction) {
        $action->tries = $tries;
        $action->timeout = $timeout;
        $action->maxExceptions = $maxExceptions;
        app()->instance($actionClass, $action);
    }

    $action->onQueue()->execute();

    $queue = Queue::connection();
    $worker = app('queue.worker');
    $worker->setCache(app('cache')->store('array'));

    for ($attempt = 1; $attempt <= ($failureAttempt ?? 3); $attempt++) {
        $job = $queue->pop();

        expect($job)->not->toBeNull()
            ->and($job->attempts())->toBe($attempt)
            ->and($job->maxTries())->toBe($tries)
            ->and($job->timeout())->toBe($timeout)
            ->and($job->maxExceptions())->toBe($maxExceptions);

        try {
            $worker->process('database', $job, new WorkerOptions('default', 0, 128, 7, 0, $workerTries));
            $this->fail('The action should throw an exception.');
        } catch (Exception $exception) {
            expect($exception->getMessage())->toBe('foobar');
        }

        $shouldFail = $attempt === $failureAttempt;

        expect($job->hasFailed())->toBe($shouldFail)
            ->and($job->isReleased())->toBe(! $shouldFail)
            ->and($queue->size())->toBe($shouldFail ? 0 : 1);
    }

    unset($_SERVER['_test_failed_message']);
})->with([
    'omitted limits use worker defaults' => [FailingAction::class, null, null, null, 2, 2],
    'null limits use worker defaults' => [QueueablePropertiesAction::class, null, null, null, 2, 2],
    'custom tries override worker defaults' => [QueueablePropertiesAction::class, 3, 9, 5, 1, 3],
    'exception limit fails before tries' => [QueueablePropertiesAction::class, 5, 9, 2, 1, 2],
    'zero tries and timeout override worker defaults' => [QueueablePropertiesAction::class, 0, 0, null, 1, null],
    'zero exceptions fails on the first exception' => [QueueablePropertiesAction::class, 0, 0, 0, 1, 1],
    'typed custom job defaults are preserved' => [FailingAction::class, 7, 90, 3, 1, 3, CustomActionJob::class],
    'action limits override typed custom job defaults' => [QueueablePropertiesAction::class, 3, 9, 5, 1, 3, CustomActionJob::class],
]);
