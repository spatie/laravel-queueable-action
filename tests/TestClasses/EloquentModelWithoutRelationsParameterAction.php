<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueueableAction\Attributes\WithoutRelations;
use Spatie\QueueableAction\QueueableAction;

#[WithoutRelations]
class EloquentModelWithoutRelationsParameterAction
{
    use QueueableAction;

    public function execute(Model $model)
    {
    }
}
