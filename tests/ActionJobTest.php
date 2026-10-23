<?php

use Spatie\QueueableAction\ActionJob;
use Spatie\QueueableAction\Tests\TestClasses\ComplexAction;
use Spatie\QueueableAction\Tests\TestClasses\DataObject;
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

it('copies the queueable properties from the action without creating dynamic properties', function () {
    $actionJob = new ActionJob(QueueablePropertiesAction::class);
    $reflection = new ReflectionObject($actionJob);

    expect($actionJob->tries)->toBe(5)
        ->and($actionJob->timeout)->toBe(120)
        ->and($actionJob->maxExceptions)->toBe(2)
        ->and($reflection->getProperty('tries')->isDefault())->toBeTrue()
        ->and($reflection->getProperty('timeout')->isDefault())->toBeTrue()
        ->and($reflection->getProperty('maxExceptions')->isDefault())->toBeTrue();
});
