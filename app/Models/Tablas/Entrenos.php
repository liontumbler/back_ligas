<?php

namespace App\Models\Tablas;

use Illuminate\Database\Eloquent\Model;

use App\Models\Tablas\Clientes;
use App\Models\Tablas\Pagos;

use App\Enums\TipoEntreno;

class Entrenos extends Model
{
    const CREATED_AT        =   'fecha_creacion';
    const UPDATED_AT        =   'fecha_modificacion';

    protected $table        =   'entrenos';
    protected $primaryKey   =   'id';

    public $incrementing    =   true;
    public $timestamps      =   true;

    protected $fillable = ['cliente_id', 'tipo', 'pago_id', 'liga_id',];

    protected $casts = [
        'cliente_id' => 'integer',
        'tipo' => TipoEntreno::class,
        'pago_id' => 'integer',
        'liga_id' => 'integer',
    ];

    public function cliente()
    {
        return $this->belongsTo(Clientes::class);
    }
    public function pago()
    {
        return $this->belongsTo(Pagos::class);
    }

    public function liga()
    {
        return $this->belongsTo(Ligas::class);
    }
    public function creador()
    {
        return $this->belongsTo(usuarios::class);
    }
    public function modificador()
    {
        return $this->belongsTo(usuarios::class);
    }
}