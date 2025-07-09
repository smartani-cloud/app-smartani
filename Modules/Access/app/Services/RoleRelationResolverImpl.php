<?php

namespace Modules\Access\Services;

use App\Models\User;
use Modules\Access\Contracts\RoleRelationResolver;

class RoleRelationResolverImpl implements RoleRelationResolver
{
    public function resolve(User $user)
    {
        return $user->belongsTo('Modules\Access\Models\Role', 'role_id');
    }
}