<?php

namespace App\Models\Tablas;

use Illuminate\Database\Eloquent\Model;

use App\Models\Tablas\Roles;

class Permisos extends Model
{
    protected $fillable = ['nombre'];

    public function roles()
    {
        return $this->belongsToMany(Roles::class, 'permiso_rol', 'permiso_id', 'rol_id');
    }
}
