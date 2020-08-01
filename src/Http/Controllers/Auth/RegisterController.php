<?php

namespace laravel\auth\journeys\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use laravel\auth\journeys\Entities\PresetUser;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Whether to allow already set users to register or not.
     *
     * @var bool
     */
    protected $allowSet = false;

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

        $this->allowSet = config('auth-journeys.ux.register.allowset');

        $this->password_complexity_level = config('auth-journeys.roles.default.complexity');

        $this->middleware('guest');

    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {

        /**
         * if a preset User is set for the registered email address check and sync possible roles set to it
         */
        if( !empty( $presetuser = $this->getPresetUser($user->email) ) ) {
            if ($presetuser->roles()->count() > 0) {
                $user->roles()->sync(
                    array_column($presetuser->roles()->get()->toArray(), 'id')
                );
            }
        }

        /**
         * push newly created password to history if users are set with the hasPasswordsHistory trait
         */
        if( method_exists( $user, 'pushCurrentToHistory' ) ) {
            $user->pushCurrentToHistory();
        }

        $this->redirectTo = $user->redirectTo();

    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                $this->passwordComplexityRule($this->password_complexity_level)
            ],
        ];

        if( !empty(config('auth-journeys.user.fields')) ) {
            foreach ( config('auth-journeys.user.fields') as $key => $field ) {
                $rules[$key] = $field['rules'];
            }
        }

        if($this->allowSet) {
            $rules['email'][] = 'exists:laj_presetusers';
        }

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $create = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'password_since' => \DB::raw('NOW()')
        ];

        if( !empty(config('auth-journeys.user.fields')) ) {
            foreach ( config('auth-journeys.user.fields') as $key => $field ) {
                $create[$key] = $data[$key];
            }
        }

        return User::create($create);
    }

    protected function getPresetUser($email) {
        return PresetUser::where('email', $email)->first();
    }

    public function showRegistrationForm() {
        return view( config('auth-journeys.ux.register.view') );
    }

    private function passwordComplexityRule($level) {
        // 0. min 8, 1. min 8, nums, letters, special, 2. min 8, nums, letters, capital letters, special,
        $regexs = [
            'regex:/^(.*).{8,}$/',
            'regex:/^(?=.*[a-z])(?=.*\d)(?=.*[$@$!%*#?&])[a-z\d$@$!%*#?&]{8,}$/',
            'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,}$/'
        ];
        return $regexs[$level] ?? $regexs[0];
    }

}
