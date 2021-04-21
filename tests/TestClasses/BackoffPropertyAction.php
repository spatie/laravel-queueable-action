<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Exception;
use Spatie\QueueableAction\QueueableAction;

class BackoffPropertyAction
{
    use QueueableAction;

    /** @var int */
    public $backoff = 5;

    public function execute()
    {
        throw new Exception("Failure with backoff property set to 5");
    }
}
