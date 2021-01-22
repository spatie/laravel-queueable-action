<?php

namespace Spatie\QueueableAction\Exceptions;

use Exception;
use Spatie\QueueableAction\ActionJob;

class InvalidConfiguration extends Exception
{
    public static function jobClassIsNotValid(string $className)
    {
        return new static("The given job class `$className` does not extend `".ActionJob::class."`");
    }
}
