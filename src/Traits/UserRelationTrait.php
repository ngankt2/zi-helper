<?php

namespace ZiBase\Traits;

use App\Models\User;

trait UserRelationTrait
{
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
