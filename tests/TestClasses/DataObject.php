<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

class DataObject
{
    public $foo;

    public function __construct(string $foo)
    {
        $this->foo = $foo;
    }
}
