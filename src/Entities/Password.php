<?php

namespace laravel\auth\journeys\Entities;

use Illuminate\Database\Eloquent\Model;

class Password extends Model {

    protected $fillable = [ 'password' ];

    protected $table = 'laj_passwords';

    public function user() {
        return $this->hasMany('App\User', 'user_id');
    }

}
