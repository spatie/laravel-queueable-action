<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Exception;
use Spatie\QueueableAction\QueueableAction;

class ClosureActionWithFailedMethod
{
    use QueueableAction;

    /** @var string */
    public $queue = 'default';

    /** @var string */
    protected $foo;

    public function __construct()
    {
        $this->foo = function() {
            return 'bar';
        };
    }

    public function execute()
    {
        return $this->foo;
    }

    public function failed(Exception $exception)
    {
        return $exception->getMessage();
    }
}
