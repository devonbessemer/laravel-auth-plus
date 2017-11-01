<?php

namespace Devon\AuthPlus\Traits;

use Devon\AuthPlus\Group;

trait Controllable
{
    /**
     * Groups that the user belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(config('authplus.models.groups', Group::class),
            config('authplus.tables.group_users', 'group_users'), 'user_id', 'group_id');
    }

    /**
     * Check if a user is a super user
     *
     * @return bool
     */
    function isSuperUser()
    {
        return (bool) $this->super_user;
    }

    /**
     * Check if a user is a certain role
     *
     * @param $role
     *
     * @return bool
     */
    function isRole($role)
    {
        return ($this->role == $role);
    }
}
