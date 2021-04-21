<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Exception;
use Spatie\QueueableAction\QueueableAction;

class BackoffAction
{
    use QueueableAction;

    public function execute()
    {
        throw new Exception("Failure with backoff strategy set to 5, 10, 15");
    }

    public function backoff(): array
    {
        return [5, 10, 15];
    }
}
