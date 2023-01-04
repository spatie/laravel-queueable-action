<?php

use Spatie\QueueableAction\ActionJob;
use Spatie\QueueableAction\Tests\TestClasses\ComplexAction;
use Spatie\QueueableAction\Tests\TestClasses\DataObject;
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
