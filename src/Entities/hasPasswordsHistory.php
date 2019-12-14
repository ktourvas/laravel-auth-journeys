<?php

namespace laravel\auth\journeys\Entities;

trait hasPasswordsHistory {

    public function passwords() {
        return $this->hasMany( 'laravel\auth\journeys\Entities\Password', 'user_id');
    }

    public function passwordConflicts( $string ) {
        foreach ($this->passwords as $index => $password) {
            if ( \Hash::check($string, $password->password) ) {
                return true;
            }
        }
        return false;
    }

}