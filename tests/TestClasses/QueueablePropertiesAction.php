<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

class QueueablePropertiesAction extends FailingAction
{
    public $tries = 5;

    public $timeout = 120;

    public $maxExceptions = 2;
}
