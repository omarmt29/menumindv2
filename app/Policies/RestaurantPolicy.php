<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;

class RestaurantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Restaurant $restaurant): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Restaurant $restaurant): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Restaurant $restaurant): bool
    {
        return $user->isSuperAdmin();
    }
}
