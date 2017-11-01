<?php

namespace Devon\AuthPlus;

use Illuminate\Database\Eloquent\Model;

/**
 * GroupPermission
 *
 * @property integer $id
 * @property integer $group_id
 * @property string $resource
 * @property string $subresource
 * @property string $ability
 * @property boolean $authorized
 * @method static \Illuminate\Database\Query\Builder|GroupPermission whereGroupId($value)
 * @method static \Illuminate\Database\Query\Builder|GroupPermission whereResource($value)
 * @method static \Illuminate\Database\Query\Builder|GroupPermission whereAbility($value)
 * @method static \Illuminate\Database\Query\Builder|GroupPermission whereAuthorized($value)
 * @mixin \Eloquent
 */
class GroupPermission extends Model
{
    public $timestamps = false;
    protected $table;
    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $this->table = config('authplus.tables.group_permissions', 'group_permissions');
        parent::__construct($attributes);
    }
}
