<?php

namespace Spatie\QueueableAction;

trait QueueableInvokeableAction
{
    use QueueableAction;

    /** @var string */
    public $queueMethod = '__invoke';
}
