<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueueableAction\QueueableAction;

class EloquentModelAction
{
    use QueueableAction;

    public function execute(Model $model)
    {
    }
}
