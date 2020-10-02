<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Spatie\QueueableAction\QueueableAction;

class MiddlewareAction
{
    use QueueableAction;

    public function execute()
    {
        //
    }

    public function middleware()
    {
        return [new ContinueMiddleware()];
    }
}
