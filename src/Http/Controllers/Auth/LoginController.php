<?php

namespace laravel\auth\journeys\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = !empty( config('auth-journeys.ux.login.redirectTo') ) ? config('auth-journeys.ux.login.redirectTo') : '/';
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated($request, $user) {

        $intended = $user->is_admin ? '/admin' : $this->redirectPath();

        if($request->ajax()) {
            return response()->json([
                'auth' => true,
                'intended' => $intended
            ]);
        }

        return redirect()->intended($intended);

    }

    public function showLoginForm() {
        return view(!empty( config('auth-journeys.ux.login.view') ) ? config('auth-journeys.ux.login.view') : 'auth.login' );
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {

        if($request->ajax()) {
            return response()->json([
                'auth' => false,
                'intended' => '/'
            ]);
        }

        return redirect()->intended('/');
    }


}
