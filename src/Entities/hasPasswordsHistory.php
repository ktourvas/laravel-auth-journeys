<?php

namespace laravel\auth\journeys\Entities;

trait hasPasswordsHistory {

    /**
     * This method is called upon instantiation of the Eloquent Model.
     * It adds the "seoMeta" field to the "$fillable" array of the model.
     *
     * @return void
     */
    public function initializehasPasswordsHistory()
    {
        $this->fillable[] = 'password_since';
    }

    public function passwords() {
        return $this->hasMany( 'laravel\auth\journeys\Entities\Password', 'user_id');
    }

    public function passwordConflicts( $string, $includeCurrent = false ) {
        foreach ( $this->passwords as $index => $password ) {
            if ( \Hash::check($string, $password->password) ) {
                return true;
            }
        }
        if( $includeCurrent
            && \Hash::check($string, $this->password)
        ) {
            return true;
        }

        return false;
    }

    public function pushCurrentToHistory() {
        $this->passwords()->updateOrCreate([
            'password' => $this->password
        ]);
    }

}