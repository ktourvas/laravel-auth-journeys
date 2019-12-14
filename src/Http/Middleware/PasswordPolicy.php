<?php

namespace laravel\auth\journeys\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PasswordPolicy
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
        if( !empty( $request->user() ) ) {
            foreach (config('auth-journeys.password') as $role => $passwordGroup) {
                if( $passwordGroup['changepolicy'] == 'days') {
                    $now = time(); // or your date as well
                    $your_date = strtotime($request->user()->passwords()->orderBy('created_at', 'desc')->first()->created_at);
                    $datediff = $now - $your_date;
                    if(
                        round($datediff / (60 * 60 * 24)) > $passwordGroup['days']
                        && $request->route()->getName() != 'password.change.form'
                        && $request->route()->getName() != 'password.change'
                    ) {
                        return redirect('password/change')->with('status', 'Password outdated. Please change your password using the form below.');
                    }
                }
            }
        }
        return $next($request);
    }
}
