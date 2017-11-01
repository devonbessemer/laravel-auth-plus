<?php

namespace Devon\AuthPlus;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Devon\AuthPlus\Contracts\DefaultPolicy;

class GateExtension
{
    /**
     * @var GateContract
     */
    protected $gate;

    /**
     * @var DefaultPolicy
     */
    protected $default;

    public function __construct(GateContract $gate, DefaultPolicy $defaultPolicy)
    {
        $this->gate    = $gate;
        $this->default = $defaultPolicy;
    }

    public function beforeCallback($user, $ability, $arguments = [])
    {
        if ($this->isSuper($user)) {
            return true;
        }

        if ($model = $this->getModel($arguments)) {

            $subresource = '';
            if (isset($arguments[1]) && is_string($arguments[1])) {
                $subresource = $arguments[1];
            }

            $authorized = $this->default->authorize($user, $ability, $model, $subresource);

            if ( ! $authorized) {
                return $authorized;
            }

            if ( ! $this->hasPolicy($model)) {
                return true;
            }

            return null;
        }

        return null;
    }

    public function isSuper($user)
    {
        return ($user->super_user == true);
    }

    public function getModel($arguments)
    {
        if ( ! isset($arguments[0])) {
            return false;
        }

        $class = $arguments[0];
        if (is_object($class)) {
            $class = get_class($class);
        }

        return $class;
    }

    public function hasPolicy($model)
    {
        try {
            $this->gate->getPolicyFor($model);
            return ($this->gate->getPolicyFor($model));
        } catch (\InvalidArgumentException $e) {
            // Ignore InvalidArgumentException
        }

        return false;
    }
}
