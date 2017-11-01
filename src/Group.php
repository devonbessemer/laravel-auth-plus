<?php

namespace Devon\AuthPlus;

use Illuminate\Database\Eloquent\Model;

/**
 * Group
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $system_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|Group whereId($value)
 * @method static \Illuminate\Database\Query\Builder|Group whereName($value)
 * @method static \Illuminate\Database\Query\Builder|Group whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|Group whereSystemId($value)
 * @method static \Illuminate\Database\Query\Builder|Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Group whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Group extends Model
{
    protected $table;

    public function __construct(array $attributes = [])
    {
        $this->table = config('authplus.tables.groups', 'groups');
        parent::__construct($attributes);
    }

    public function permissions()
    {
        return $this->hasMany(config('authplus.models.group_permissions', GroupPermission::class), 'group_id');
    }

    public function users()
    {
        return $this->belongsToMany(config('authplus.models.users', 'App\User'),
            config('authplus.tables.group_users', 'group_users'), 'group_id', 'user_id');
    }
}
