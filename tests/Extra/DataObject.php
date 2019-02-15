<?php

namespace Spatie\QueueableAction\Tests\Extra;

class DataObject
{
    public $foo;

    public function __construct(string $foo)
    {
        $this->foo = $foo;
    }
}
