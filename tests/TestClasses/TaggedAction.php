<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Spatie\QueueableAction\QueueableAction;

class TaggedAction
{
    use QueueableAction;

    public function execute()
    {
        //
    }

    public function tags()
    {
        return [
            'custom_tag',
            'tagged_action',
        ];
    }
}
