<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Exception;
use Spatie\QueueableAction\QueueableAction;

class ActionWithFailedMethod
{
    use QueueableAction;

    public function execute()
    {
        //
    }

    public function failed(Exception $exception)
    {
        return $exception->getMessage();
    }
}
