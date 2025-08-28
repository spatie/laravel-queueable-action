<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueueableAction\Attributes\WithoutRelations;
use Spatie\QueueableAction\QueueableAction;

class EloquentModelWithoutRelationsAction
{
    use QueueableAction;

    public function execute(#[WithoutRelations] Model $model)
    {
    }
}
