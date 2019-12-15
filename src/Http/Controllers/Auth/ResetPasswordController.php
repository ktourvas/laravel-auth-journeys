<?php

namespace laravel\auth\journeys\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords {
        reset as protected oreset;
    }

    /**
     * password complexity level
     */
    protected $roleRule = 0;

    /**
     * Where to redirect users after resetting their password.
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
        $this->middleware('guest');
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view( config('auth-journeys.ux.password.reset') )->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function reset(Request $request)
    {

        $user = $this->broker()->getUser( $this->credentials( $request ) );

        if( !empty( $user ) ) {
            if( $this->broker()->tokenExists($user, $request->token) ) {
                $user->pushCurrentToHistory();
                if( $user->passwordConflicts( $request->password ) ) {
                    return redirect()->back()->withErrors([ 'password' => 'invalid new password' ]);
                }
                $this->roleRule = $user->passwordRule();
                $this->redirectTo = $user->redirectTo();

            }
        }

        return $this->oreset( $request );

    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8|'.$this->roleRule,
        ];
    }

}
