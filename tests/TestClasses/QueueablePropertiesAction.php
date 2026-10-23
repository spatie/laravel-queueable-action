<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Spatie\QueueableAction\QueueableAction;

class QueueablePropertiesAction
{
    use QueueableAction;

    public $tries = 5;

    public $timeout = 120;

    public $maxExceptions = 2;

    public function execute()
    {
        //
    }
}
