<?php

use Devon\AuthPlus\Group;
use Devon\AuthPlus\GroupPermission;

require_once __DIR__ . '/BaseTest.php';

class AuthorizationPlusTest extends BaseTest
{
    public $user;
    public $admin;
    public $superuser;

    public function setUp()
    {
        parent::setUp();

        $this->superuser = factory(User::class)->create(['role' => 'admin', 'super_user' => true]);
        $this->admin     = factory(User::class)->create(['role' => 'admin']);
        $this->user      = factory(User::class)->create(['role' => 'user']);
        $this->group     = factory(Group::class)->create();
    }

    public function test_setup()
    {
        $this->assertTrue(true);
    }

    public function test_that_role_method_is_on_user_model()
    {
        $this->assertTrue(method_exists($this->user, 'isRole'),
            'isRole method does not exist.  Is User model using Controllable?');
        $this->assertTrue($this->user->isRole('user'));
        $this->assertTrue($this->admin->isRole('admin'));
        $this->assertFalse($this->user->isRole('admin'));
        $this->assertFalse($this->admin->isRole('user'));
    }

    public function test_user_privileges_are_revoked_by_default()
    {
        $this->assertFalse($this->user->can('read', 'some-faulty-resource'));
    }

    public function test_super_user_privileges_are_allowed_by_default()
    {
        $this->assertTrue($this->superuser->can('read', 'some-faulty-resource'));
    }

    public function test_user_can_be_assigned_to_a_group()
    {
        $this->user->groups()->attach($this->group);
        $this->assertCount(1, $this->user->groups);
    }

    public function test_user_can_be_granted_privilege_to_a_named_resource()
    {
        $this->user->groups()->attach($this->group);

        $this->assertFalse($this->user->can('read', 'named_resource'),
            'User had access to named_resource before access was given.');

        $permission = GroupPermission::make([
            'resource'   => 'named_resource',
            'ability'    => 'read',
            'authorized' => true,
        ]);
        $this->group->permissions()->save($permission);

        $permission = GroupPermission::make([
            'resource'   => 'named_resource',
            'ability'    => 'write',
            'authorized' => true,
        ]);
        $this->group->permissions()->save($permission);

        $this->assertTrue($this->user->can('read', 'named_resource'),
            'User unable to read named_resource after access was granted.');
        $this->assertTrue($this->user->can('write', 'named_resource'),
            'User unable to write named_resource after access was granted.');

    }

    public function test_user_can_be_granted_privileges_to_a_model_resource()
    {
        $this->user->groups()->attach($this->group);

        $this->assertFalse($this->user->can('read', $this->admin),
            'User had access to User model before access was given.');

        $permission = GroupPermission::make([
            'resource'   => User::class,
            'ability'    => 'read',
            'authorized' => true,
        ]);
        $this->group->permissions()->save($permission);

        $this->assertTrue($this->user->can('read', $this->admin),
            'User unable to read User model after access was granted.');
    }

    public function test_user_can_be_granted_privileges_to_a_subresource()
    {
        $this->user->groups()->attach($this->group);

        $this->assertFalse($this->user->can('read', [$this->admin, 'address.line1']),
            'User had access to subresource before access was given.');

        $permission = GroupPermission::make([
            'resource'    => User::class,
            'subresource' => 'address.line1',
            'ability'     => 'read',
            'authorized'  => true,
        ]);
        $this->group->permissions()->save($permission);

        $this->assertTrue($this->user->can('read', [$this->admin, 'address.line1']),
            'User could not access subresource after access was given.');
    }

    public function test_user_can_be_granted_privileges_to_a_subresource_via_wildcard()
    {
        $this->user->groups()->attach($this->group);

        $this->assertFalse($this->user->can('read', [$this->admin, 'address.line1']),
            'User had access to subresource before access was given.');

        $permission = GroupPermission::make([
            'resource'    => User::class,
            'subresource' => 'address.*',
            'ability'     => 'read',
            'authorized'  => true,
        ]);
        $this->group->permissions()->save($permission);

        $this->assertTrue($this->user->can('read', [$this->admin, 'address.line1']),
            'User could not access subresource after access was given.');
    }
}
