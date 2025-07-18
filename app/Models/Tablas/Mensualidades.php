<?php

namespace App\Models\Tablas;

use Illuminate\Database\Eloquent\Model;

use App\Models\Tablas\Clientes;
use App\Models\Tablas\Entrenos;

class Mensualidades extends Model
{
    const CREATED_AT        =   'fecha_creacion';
    const UPDATED_AT        =   'fecha_modificacion';

    protected $table        =   'mensualidades';
    protected $primaryKey   =   'id';

    public $incrementing    =   true;
    public $timestamps      =   true;

    protected $fillable = ['cliente_id', 'fecha_inicio', 'fecha_fin', 'sesiones_disponibles', 'sesiones_usadas'];

    public function usuario()
    {
        return $this->belongsTo(Clientes::class);
    }
    public function entrenos()
    {
        return $this->hasMany(Entrenos::class);
    }
}
