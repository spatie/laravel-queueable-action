<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Spatie\QueueableAction\QueueableInvokeableAction;
use Spatie\QueueableAction\Tests\TestCase;

class InvokeableAction
{
    use QueueableInvokeableAction;

    /** @var string */
    public $queue = 'default';

    /** @var \Spatie\QueueableAction\Tests\TestClasses\DependencyObject */
    protected $dependencyObject;

    public function __construct(DependencyObject $dependencyObject)
    {
        $this->dependencyObject = $dependencyObject;
    }

    public function __invoke(DataObject $dataObject)
    {
        file_put_contents(
            TestCase::LOG_PATH,
            'Invoked: '.$dataObject->foo.' '.$this->dependencyObject->bar
        );
    }
}
