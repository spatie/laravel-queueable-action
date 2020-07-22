<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Exception;
use Spatie\QueueableAction\QueueableAction;

class FailingAction
{
    use QueueableAction;

    public function execute()
    {
        throw new Exception('foobar');
    }

    public function failed(Exception $exception)
    {
        $_SERVER['_test_failed_message'] = $exception->getMessage();
    }
}
