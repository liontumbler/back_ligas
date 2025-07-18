<?php

namespace App\Models\Tablas;

use Illuminate\Database\Eloquent\Model;

use App\Models\Tablas\Clientes;

class Pagos extends Model
{
    const CREATED_AT        =   'fecha_creacion';
    const UPDATED_AT        =   'fecha_modificacion';

    protected $table        =   'pagos';
    protected $primaryKey   =   'id';

    public $incrementing    =   true;
    public $timestamps      =   true;

    protected $fillable = ['cliente_id', 'tipo', 'valor', 'fecha_pago', 'estado'];

    public function cliente()
    {
        return $this->belongsTo(Clientes::class);
    }

    public function liga()
    {
        return $this->belongsTo(Ligas::class);
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
