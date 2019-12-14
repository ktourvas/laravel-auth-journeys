<?php

namespace laravel\auth\journeys\Entities;

trait hasPasswordsHistory {

    public function passwords() {
        return $this->hasMany( 'laravel\auth\journeys\Entities\Password', 'user_id');
    }

}