<?php

namespace App\Models\Tablas;

use Illuminate\Database\Eloquent\Model;

use App\Models\Tablas\Usuarios;

class Ligas extends Model
{
    const CREATED_AT        =   'fecha_creacion';
    const UPDATED_AT        =   'fecha_modificacion';

    protected $table        =   'ligas';
    protected $primaryKey   =   'id';

    public $incrementing    =   true;
    public $timestamps      =   true;

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
    ];
}
