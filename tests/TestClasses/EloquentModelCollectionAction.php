<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\QueueableAction\QueueableAction;

class EloquentModelCollectionAction
{
    use QueueableAction;

    /** @param Collection<Model> $models */
    public function execute(Collection $models)
    {
    }
}
