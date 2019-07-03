<?php

namespace Softlab\History\Admin\Policies;

use Softlab\History\Admin\Http\Sections\Histories;
use Softlab\History\Models\History;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;

class HistoriesSectionModelPolicy
{
    use HandlesAuthorization;

/**
     * @param User $user
     * @param string $ability
     * @param Roles $section
     * @param Role $item
     *
     * @return bool
     */
    // public function before(User $user, $ability, Roles $section, Role $item)
    // {
    //         return false;
    //     if ($user->isAdmin()) {
    //         if ($ability != 'display' && $ability != 'create' && $item->id <= 2) {
    //             return false;
    //         }

    //         return true;
    //     }
    // }

    /**
     * @param User $user
     * @param Roles $section
     * @param Role $item
     *
     * @return bool
     */
    public function display(User $user, Histories $section, History $item)
    {
        return $user->isAdmin();
    }

    public function create(User $user, Histories $section, History $item)
    {
        return false;//$user->isAdmin();
    }

    /**
     * @param User $user
     * @param Roles $section
     * @param Role $item
     *
     * @return bool
     */
    public function edit(User $user, Histories $section, History $item)
    {
        return false;//$user->isAdmin();
    }

    /**
     * @param User $user
     * @param Roles $section
     * @param Role $item
     *
     * @return bool
     */
    public function delete(User $user, Histories $section, History $item)
    {
        return false;//$user->isAdmin();
    }
}
