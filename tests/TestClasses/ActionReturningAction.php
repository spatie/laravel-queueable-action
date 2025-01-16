<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Spatie\QueueableAction\QueueableAction;

/**
 * @property $job
 */
class ActionReturningAction
{
    use QueueableAction;

    public function execute()
    {
        return $this->job;
    }
}