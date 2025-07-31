<?php

namespace App\Models\Tablas;

use Illuminate\Database\Eloquent\Model;

use App\Enums\EstadoLicencia;

class Licencias extends Model
{
    const CREATED_AT        =   'fecha_creacion';
    const UPDATED_AT        =   'fecha_modificacion';

    protected $table        =   'licencias';
    protected $primaryKey   =   'id';

    public $incrementing    =   true;
    public $timestamps      =   true;

    protected $fillable = ['codigo', 'valor', 'fecha_inicio', 'fecha_fin', 'estado'];

    protected $casts = [
        'codigo' => 'string',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'valor' => 'decimal:2',
        'estado' => EstadoLicencia::class
    ];

    public function creador()
    {
        return $this->belongsTo(Usuarios::class, 'usuario_creacion');
    }

    public function modificador()
    {
        return $this->belongsTo(Usuarios::class, 'usuario_modificacion');
    }
}
