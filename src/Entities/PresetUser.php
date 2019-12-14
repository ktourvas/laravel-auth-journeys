<?php

namespace laravel\auth\journeys\Entities;

use Illuminate\Database\Eloquent\Model;

class PresetUser extends Model {

    protected $fillable = [ 'email' ];

    protected $table = 'laj_presetusers';

    public function roles() {
        return $this->hasMany('laravel\auth\journeys\Entities\PresetUserRole', 'preset_user_id');
    }

}
