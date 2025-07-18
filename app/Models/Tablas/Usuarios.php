<?php

namespace App\Models\Tablas;

use Illuminate\Database\Eloquent\Model;


class Usuarios extends Model
{
    const CREATED_AT        =   'fecha_creacion';
    const UPDATED_AT        =   'fecha_modificacion';

    protected $table        =   'usuarios';
    protected $primaryKey   =   'id';

    public $incrementing    =   true;
    public $timestamps      =   true;

    protected $fillable = [
        'nombres',
        'apellidos',
        'correo',
        'password'
    ];

    protected $hidden = [
        'password'
    ];

    public function liga()
    {
        return $this->belongsTo(Ligas::class);
    }

    public function creador()
    {
        return $this->belongsTo(self::class, 'usuario_creacion');
    }

    public function modificador()
    {
        return $this->belongsTo(self::class, 'usuario_modificacion');
    }

    public function usuariosCreados()
    {
        return $this->hasMany(self::class, 'usuario_creacion');
    }

    public function usuariosModificados()
    {
        return $this->hasMany(self::class, 'usuario_modificacion');
    }
}
