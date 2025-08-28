<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModelSerializationUser extends Model
{
    public $table = 'users';
    public $guarded = [];
    public $timestamps = false;
    
    /**
     * @return HasMany<self, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

}
