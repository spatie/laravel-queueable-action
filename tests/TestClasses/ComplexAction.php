<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Spatie\QueueableAction\QueueableAction;
use Spatie\QueueableAction\Tests\TestCase;

class ComplexAction
{
    use QueueableAction;

    /** @var string */
    public $queue = 'default';

    /** @var \Spatie\QueueableAction\Tests\TestClasses\DependencyObject */
    protected $dependencyObject;

    public function __construct(DependencyObject $dependencyObject)
    {
        $this->dependencyObject = $dependencyObject;
    }

    public function execute(DataObject $dataObject)
    {
        file_put_contents(
            TestCase::LOG_PATH,
            $dataObject->foo.' '.$this->dependencyObject->bar
        );
    }
}
