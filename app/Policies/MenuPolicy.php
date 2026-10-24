<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;

class MenuPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->restaurant_id !== null;
    }

    public function view(User $user, Menu $menu): bool
    {
        return $this->owns($user, $menu);
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->restaurant_id !== null;
    }

    public function update(User $user, Menu $menu): bool
    {
        return $this->owns($user, $menu);
    }

    public function delete(User $user, Menu $menu): bool
    {
        return $this->owns($user, $menu);
    }

    private function owns(User $user, Menu $menu): bool
    {
        return $user->isSuperAdmin() || $user->restaurant_id === $menu->restaurant_id;
    }
}
