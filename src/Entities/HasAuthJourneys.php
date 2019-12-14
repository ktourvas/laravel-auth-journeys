<?php

namespace laravel\auth\journeys\Entities;

trait HasAuthJourneys
{

    public function redirectTo() {
        $intended = '/';
        foreach ( config('auth-journeys.roles') as $role => $rules ) {
            if( !empty($rules['redirectTo'])
                && $this->userIs($role)
            ) {
                $intended = $rules['redirectTo'];
            }
        }
        return $intended ;
    }

    public function validateCurrent($sent) {
        return \Hash::check($sent, $this->password);
    }

    public function passwordRule() {
        // 0. min 8, 1. min 8, nums, letters, special, 2. min 8, nums, letters, capital letters, special,
        $regexs = [
            'regex:/^(.*).{8,}$/',
            'regex:/^(?=.*[a-z])(?=.*\d)(?=.*[$@$!%*#?&])[a-z\d$@$!%*#?&]{8,}$/',
            'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,}$/'
        ];
        return $regexs[ $this->passwordComplexityLevel() ] ?? $regexs[0];
    }

    public function passwordComplexityLevel() {
        foreach ($this->roles as $role) {
            if(
            in_array($role->name, array_keys( config('auth-journeys.roles') ) )
            ) {
                return config('auth-journeys.roles')[$role->name]['complexity'];
            }
        }
        return 0;
    }

    public function changePassword($new) {
        $hash = \Hash::make( $new );

        $this->passwords()->create([
            'password' => $hash
        ]);

        // Save user with new password
        $this->update([
            'password' => $hash,
        ]);

        // remove oldest password from list if count is over 9
        if( $this->passwords()->count() > 9 ) {
            $this->passwords()->orderBy('created_at', 'asc')->limit(1)->delete();
        }
    }

}