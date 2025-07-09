<?php

namespace Modules\Access\Contracts;

use App\Models\User;

interface RoleRelationResolver
{
    public function resolve(User $user);
}