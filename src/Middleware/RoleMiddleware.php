<?php

namespace Devon\AuthPlus\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param array $roles
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if ( ! $user = $request->user()) {
            return redirect('/login');
        }
        if ( ! in_array($user->role, $roles)) {
            return redirect('/login');
        }

        return $next($request);
    }

}