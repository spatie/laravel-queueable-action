<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Illuminate\Database\Eloquent\Model;

class ModelSerializationUser extends Model
{
    public $table = 'users';
    public $guarded = [];
    public $timestamps = false;
}
