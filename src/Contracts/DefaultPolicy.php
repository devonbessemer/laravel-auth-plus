<?php

namespace Devon\AuthPlus\Contracts;

interface DefaultPolicy
{
    public function authorize($user, $ability, $model);
}
