<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use DateTime;
use Exception;
use Spatie\QueueableAction\QueueableAction;

class RetryUntilAction
{
    use QueueableAction;

    public function retryUntil()
    {
        return DateTime::createFromFormat("Y-m-d H:m:s", "2000-01-01 00:00:00");
    }

    public function execute()
    {
    }
}
