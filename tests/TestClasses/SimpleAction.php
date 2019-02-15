<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Spatie\QueueableAction\QueueableAction;

class SimpleAction
{
    use QueueableAction;

    public function execute()
    {
        //
    }
}
