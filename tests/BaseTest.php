<?php

use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\TestCase;

abstract class BaseTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testing']);
        $this->artisan('migrate', [
            '--database' => 'testing',
//            '--realpath' => realpath(__DIR__.'/../database/migrations'),
        ]);
        $this->withFactories(__DIR__.'/../database/factories');
        $this->setConfig();
    }

    protected function getPackageProviders($app)
    {
        return [
            \Devon\AuthPlus\AuthPlusServiceProvider::class,
        ];
    }

    protected function setConfig()
    {
        Config::set([
            'authplus.models' => [
                'groups'            => \Devon\AuthPlus\Group::class,
                'group_permissions' => \Devon\AuthPlus\GroupPermission::class,
                'users'             => User::class,
            ],
            'authplus.tables' => [
                'groups'            => 'groups',
                'group_permissions' => 'group_permissions',
                'users'             => 'users',
                'group_users'       => 'group_users',
            ],
        ]);
    }

}

class User extends Model {
    use \Devon\AuthPlus\Traits\Controllable;
    use \Illuminate\Foundation\Auth\Access\Authorizable;

}