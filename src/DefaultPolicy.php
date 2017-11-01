<?php

namespace Devon\AuthPlus;

use Devon\AuthPlus\Contracts\DefaultPolicy as DefaultPolicyContract;
use Illuminate\Support\Facades\Cache;

class DefaultPolicy implements DefaultPolicyContract
{
    protected $cacheMinutes = false;

    /**
     * @param $user
     * @param string $ability
     * @param string $model
     * @param string $subresource
     *
     * @return bool|null  Returns null if no qualified ability is defined, return the authorized value (bool) otherwise
     */
    public function authorize($user, $ability, $model, $subresource = '')
    {
        if (is_object($model)) {
            $model = get_class($model);
        }

        $permissions = $this->getPermissions($user);

        return $this->evaluatePermission($ability, $model, $subresource, $permissions);
    }

    /**
     * Return the complete list of permissions granted to the user
     *
     * @param $user
     *
     * @return array|bool|mixed
     */
    public function getPermissions($user)
    {
        $userId = $user->{$user->getKeyName()};

        if ($cached = $this->getCachedPermissions($userId)) {
            return $cached;
        }

        $groups = Group::with('permissions')
                       ->whereHas('users', function ($query) use ($user) {
                           $query->where($user->getTable() . '.' . $user->getKeyName(), $user->{$user->getKeyName()});
                       })
                       ->get();

        $permissions = [];

        foreach ($groups as $group) {
            foreach ($group->permissions as $permission) {
                $qualifiedAbility = $permission->ability . ':' . $permission->resource . '.' . $permission->subresource;
                if ( ! isset($permissions[$qualifiedAbility])
                     || $permissions[$qualifiedAbility] < $permission->authorized
                ) {
                    $permissions[$qualifiedAbility] = $permission->authorized;
                }
            }
        }

        $this->setCachedPermissions($userId, $permissions);
        return $permissions;
    }

    /**
     * Evaluate the requested access against the user's available permissions
     *
     * @param $ability
     * @param $model
     * @param $subresource
     * @param $permissions
     *
     * @return bool|null
     */
    function evaluatePermission($ability, $model, $subresource, $permissions)
    {
        $possibilities = $this->generatePossibleQualifiedAbilities($ability, $model, $subresource);
        foreach ($possibilities as $possibility) {
            // The following line can be used to debug the list of possible abilities checked
            // echo('Checking qualified ability: ' . $possibility . PHP_EOL);
            if (isset($permissions[$possibility])) {
                return (bool)$permissions[$possibility];
            }
        }

        return null;
    }

    /**
     * Generate the complete list of possible subresources from most specific to least specific (address.line.1 to address)
     *
     * @param $ability
     * @param $model
     * @param string $subresource
     *
     * @return array
     */
    function generatePossibleQualifiedAbilities($ability, $model, $subresource = '')
    {
        if ($subresource) {
            $possibilities[] = $ability . ':' . $model . '.' . $subresource;

            $parts = explode('.', $subresource);
            if (count($parts) > 1) {
                for ($i = count($parts); $i > 1; $i--) {
                    $parts           = array_slice($parts, 0, -1);
                    $possibilities[] = $ability . ':' . $model . '.' . implode('.', $parts) . '.*';
                    $possibilities[] = $ability . ':' . $model . '.' . implode('.', $parts);
                }
            }
        }

        $possibilities[] = $ability . ':' . $model . '.*';

        return $possibilities;
    }

    /**
     * Return the cached permissions of a user if caching is enabled and cache exists
     *
     * @param $user_id
     *
     * @return bool|mixed
     */
    function getCachedPermissions($user_id)
    {
        if ($this->cacheMinutes === false) return false;

        $cacheKeyName = $this->getPermissionsCacheKey($user_id);
        if (Cache::has($cacheKeyName)) {
            return Cache::get($cacheKeyName);
        }

        return false;
    }

    /**
     * Store the cached permissions if caching is enabled
     *
     * @param $user_id
     * @param $permissions
     */
    function setCachedPermissions($user_id, $permissions)
    {
        if ($this->cacheMinutes === false) return;

        Cache::put($this->getPermissionsCacheKey($user_id), $permissions, $this->cacheMinutes);
    }

    /**
     * Return the cache key used for permissions
     *
     * @param $user_id
     *
     * @return string
     */
    function getPermissionsCacheKey($user_id)
    {
        return 'permissions.user.' . $user_id;
    }
}
