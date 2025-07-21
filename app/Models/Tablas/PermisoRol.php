<?php

namespace App\Models\Tablas;

use Illuminate\Database\Eloquent\Model;

class PermisoRol extends Model
{
    const CREATED_AT        =   'fecha_creacion';
    const UPDATED_AT        =   'fecha_modificacion';

    protected $table        =   'permiso_rol';
    protected $primaryKey   =   'id';

    public $incrementing    =   true;
    public $timestamps      =   true;

    protected $fillable = [
        'role_id',
        'permission_id',
    ];
}
