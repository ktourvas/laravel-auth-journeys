<?php

namespace laravel\auth\journeys\Entities;

use Illuminate\Database\Eloquent\Model;

class PresetUserRole extends Model {

    protected $fillable = [ 'email' ];

    protected $table = 'laj_presetuser_roles';

    public function roles() {
        return $this->belongsTo('laravel\auth\journeys\Entities\PresetUser', 'presetuser_id');
    }

}
