<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueueableAction\Attributes\WithoutRelations;
use Spatie\QueueableAction\QueueableAction;

#[WithoutRelations]
class EloquentModelWithoutRelationsClassAction
{
    use QueueableAction;

    public function execute(Model $model)
    {
    }
}
