<?php

namespace laravel\auth\journeys\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class PasswordController extends Controller {

    /**
     * What level of password complexity to use.
     *
     * @var bool
     */
    protected $password_complexity_level = 0;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        $this->password_complexity_level = config('auth-journeys.password.default.complexity');

        $this->middleware('auth');

    }

    public function showChangePassword(Request $request) {
        return view('laj::passwordchange', [
            'status' => $request->session()->get('status') ?? null
        ]);
    }

    public function changePassword(Request $request) {

        $this->validate( $request, [
                'password' => [
                    'required',
                ],
                'new_password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    $this->passwordComplexityRule($this->password_complexity_level)
                ],
            ]
        );

        if( !\Hash::check($request->password, $request->user()->password) ) {
            return  redirect()->back()->withErrors([ 'password' => 'incorrect password' ]);
        }

        foreach ($request->user()->passwords as $index => $password) {
            if ( \Hash::check($request->new_password, $password->password) ) {
                return redirect()->back()->withErrors([ 'new_password' => 'invalid new password' ]);
            }
        }

        $request->user()->passwords()->create([
            'password' => $request->user()->password
        ]);

        if( $request->user()->passwords()->count() > 9 ) {
            $request->user()->passwords()->orderBy('created_at', 'asc')->limit(1)->delete();
        }

        $request->user()->update([
            'password' => \Hash::make( $request->new_password ),
        ]);

        return redirect('/');

    }

    private function passwordComplexityRule($level) {
        // 0. min 8, 1. min 8, nums, letters, special, 2. min 8, nums, letters, capital letters, special,
        $regexs = [
            'regex:/^{8,}$/',
            'regex:/^(?=.*[a-z])(?=.*\d)(?=.*[$@$!%*#?&])[a-z\d$@$!%*#?&]{8,}$/',
            'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,}$/'
        ];
        return $regexs[$level] ?? $regexs[0];
    }

}
