<?php

namespace Devon\AuthPlus;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
//use OwenIt\Auditing\AuditingServiceProvider;

class AuthPlusServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    protected $auditProvider;

    /**
     * Bootstrap the application services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     *
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');

        $this->publishes([
            __DIR__ . '/../config/' => config_path()
        ], 'config');

        $gate->before(function ($user, $ability, $arguments = []) use ($gate) {
            $ext = new GateExtension($gate, new DefaultPolicy());

            return $ext->beforeCallback($user, $ability, $arguments);
        });

//        $this->auditProvider->boot();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
//        $this->auditProvider = new AuditingServiceProvider($this->app);
//        $this->auditProvider->register();
    }

//    public function provides()
//    {
//        return $this->auditProvider->provides();
//    }

}
