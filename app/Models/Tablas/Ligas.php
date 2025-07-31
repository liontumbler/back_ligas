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

    protected $fillable = ['nombre', 'direccion', 'telefono',];

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class);
    }

    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class);
    }

    public function planes(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function entrenos(): HasMany
    {
        return $this->hasMany(Entreno::class);
    }

    public function creador()
    {
        return $this->belongsTo(Usuarios::class, 'usuario_creacion');
    }

    public function modificador()
    {
        return $this->belongsTo(Usuarios::class, 'usuario_modificacion');
    }
}
