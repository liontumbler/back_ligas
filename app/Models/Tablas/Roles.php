<?php

namespace App\Models\Tablas;

use Illuminate\Database\Eloquent\Model;

use App\Models\Tablas\Permisos;

class Roles extends Model
{
    const CREATED_AT        =   'fecha_creacion';
    const UPDATED_AT        =   'fecha_modificacion';

    protected $table        =   'roles';
    protected $primaryKey   =   'id';

    public $incrementing    =   true;
    public $timestamps      =   true;

    protected $fillable = [
        'name',
        'usuario_creacion',
    ];

    public function permisos()
    {
        return $this->belongsToMany(Permisos::class, 'permiso_rol', 'rol_id', 'permiso_id');
    }

    public function usuarios()
    {
        return $this->hasMany(Usuarios::class, 'rol_id');
    }
}
