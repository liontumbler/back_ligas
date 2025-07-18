<?php

namespace App\Models\Tablas;

use Illuminate\Database\Eloquent\Model;

use App\Models\Tablas\Permisos;

class Roles extends Model
{
    protected $fillable = ['nombre'];

    public function permisos()
    {
        return $this->belongsToMany(Permisos::class, 'permiso_rol', 'rol_id', 'permiso_id');
    }
}
