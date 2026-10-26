<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Spatie\QueueableAction\ActionJob;

class CustomActionJob extends ActionJob
{
    public int $tries = 7;

    public int $timeout = 90;

    public int $maxExceptions = 3;
}
