<?php

namespace laravel\auth\journeys\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class InactiveLogout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        /**
         * Presumes that the number retrieved from the database is always the last activity and not changed by any
         * other middleware process before it reaches the controller method
         */
        $last = \DB::table('sessions')->where('id', $request->session()->getId())->first();

        if (
            !empty($last)
            && Auth::guard($guard)->check()
            && $request->user()->userIs('admin')
            && ( time() - $last->last_activity) > 1800
        ) {
            \Auth::logout();
            $request->session()->invalidate();
            return redirect('/login');
        }

        return $next($request);
    }
}
