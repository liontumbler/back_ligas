<?php

namespace App\Models\Tablas;

use Illuminate\Database\Eloquent\Model;

use App\Models\Tablas\Clientes;
use App\Models\Tablas\Pagos;
use App\Models\Tablas\Mensualidades;

class Entrenos extends Model
{
    const CREATED_AT        =   'fecha_creacion';
    const UPDATED_AT        =   'fecha_modificacion';

    protected $table        =   'entrenos';
    protected $primaryKey   =   'id';

    public $incrementing    =   true;
    public $timestamps      =   true;

    protected $fillable = ['cliente_id', 'fecha', 'tipo', 'pago_id', 'mensualidad_id'];

    public function usuario()
    {
        return $this->belongsTo(Clientes::class);
    }
    public function pago()
    {
        return $this->belongsTo(Pagos::class);
    }
    public function mensualidad()
    {
        return $this->belongsTo(Mensualidades::class);
    }
}
