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

        $this->password_complexity_level = config('auth-journeys.password.default.complexity');

        $this->middleware('guest');

    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {

        $this->validator($request->all())->validate();

        if($this->allowSet) {

            $presetuser = $this->getPresetUser($request->email);

            if( empty($presetuser) ) {
                return;
            }

        }

        event(new Registered($user = $this->create($request->all())));

        $user->passwords()->create([
            'password' => Hash::make($request->password),
        ]);

        if( $presetuser->roles()->count() > 0 ) {
            $user->roles()->sync( array_column( $presetuser->roles()->get()->toArray() , 'role_id') );
        }

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
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
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Update the pre-set user instance.
     *
     * @param  array  $data
     * @return bool
     */
    protected function update($user, array $data)
    {
        return $user->update([

            'password' => Hash::make($data['password']),

            'name' => $data['name'],

        ]);
    }

    protected function getPresetUser($email) {
        return PresetUser::where('email', $email)->first();
    }

    public function showRegistrationForm() {
        return view(!empty( config('auth-journeys.ux.register.view') ) ? config('auth-journeys.ux.register.view') : 'auth.register' );
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
