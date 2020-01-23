<?php

namespace laravel\auth\journeys\Entities;

use Illuminate\Database\Eloquent\Model;
use ktourvas\rolesandperms\Entities\HasRoles;

class PresetUser extends Model {

    use HasRoles;

    protected $fillable = [ 'email' ];

    protected $table = 'laj_presetusers';

}
