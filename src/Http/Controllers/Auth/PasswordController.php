<?php

namespace laravel\auth\journeys\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class PasswordController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Displays the user with the password change form.
     *
     * @param Request $request
     * @return mixed
     */
    public function showChangePassword(Request $request) {
        return view('laj::passwordchange', [
            'status' => $request->session()->get('status') ?? null
        ]);
    }

    /**
     * Validate request and change user's password
     * @param Request $request
     * @return mixed
     */
    public function changePassword(Request $request) {

        // Validate Form Post
        $this->validate( $request, [
            'password' => [
                'required',
            ],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                $request->user()->passwordRule()
            ],
        ]);

        // Validate current password
        if( !$request->user()->validateCurrent($request->password) ) {
            return  redirect()->back()->withErrors([ 'password' => 'incorrect password' ]);
        }

        // Check for password conflicts
        if( $request->user()->passwordConflicts($request->new_password, true ) ) {
            return redirect()->back()->withErrors([ 'new_password' => 'invalid new password' ]);
        }

        $request->user()->changePassword( $request->new_password );

        return redirect( $request->user()->redirectTo() );
    }

}
