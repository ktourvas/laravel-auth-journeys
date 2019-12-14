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
        if( \Auth::check() ) {
            foreach (config('auth-journeys.roles') as $role => $rules ) {
                if( $rules['changepolicy'] == 'days'
                    && !empty($rules['days'])
                    && $request->user()->userIs($role)
                ) {

                    // Start off over the time period
                    $days = $rules['days']+1;

                    // Set true one if there is at least one password saved
                    if( !empty( $request->user()->passwords()->orderBy('created_at', 'desc')->first() ) ) {
                        $days =
                            round(
                                ( time() - strtotime($request->user()->passwords()->orderBy('created_at', 'desc')->first()->created_at ))
                                /
                                (60 * 60 * 24)
                            );
                    }

                    if(
                        $days > $rules['days']
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
