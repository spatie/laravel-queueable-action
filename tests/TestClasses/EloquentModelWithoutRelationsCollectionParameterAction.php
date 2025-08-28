<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\QueueableAction\Attributes\WithoutRelations;
use Spatie\QueueableAction\QueueableAction;

class EloquentModelWithoutRelationsCollectionParameterAction
{
    use QueueableAction;

    /** @param Collection<Model> $models */
    public function execute(#[WithoutRelations] Collection $models)
    {
    }
}
