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
        'liga_id',
        'nombres',
        'apellidos',
        'correo',
        'password'
    ];

    protected $hidden = [
        'password',
        'rol'
    ];

    protected $casts = [
        'nombres' => 'string',
        'apellidos' => 'string',
        'correo' => 'string',
        'password' => 'string',
        'liga_id' => 'integer',
        'rol_id' => 'integer',
    ];

    protected $appends = [
        'rol_name',
        //'permisos_rol'
    ];

    public function getRolNameAttribute()
    {
        return $this->rol->nombre;
    }

    // public function getPermisosRolAttribute()
    // {
    //     return $this->rol ? $this->rol->permisos : collect();
    // }

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

    public function rol()
    {
        return $this->belongsTo(Roles::class, 'rol_id');
    }

    public function permisos()
    {
        return $this->rol ? $this->rol->permisos : collect();
    }
}