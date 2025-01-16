<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Spatie\QueueableAction\QueueableAction;

/**
 * @property $job
 */
class ActionReturningJob
{
    use QueueableAction;

    public function execute()
    {
        return $this->job;
    }
}