<?php

namespace App\Models\Tablas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Tablas\Pagos;
use App\Models\Tablas\Entrenos;

class Clientes extends Model
{
    const CREATED_AT        =   'fecha_creacion';
    const UPDATED_AT        =   'fecha_modificacion';

    protected $table        =   'clientes';
    protected $primaryKey   =   'id';

    public $incrementing    =   true;
    public $timestamps      =   true;

    protected $fillable = ['nombres', 'apellidos', 'correo', 'telefono', 'liga_id'];

    public function equipo()
    {
        return $this->BelongsTo(Equipos::class);
    }
    public function plan()
    {
        return $this->BelongsTo(planes::class);
    }
    public function liga()
    {
        return $this->BelongsTo(Ligas::class);
    }
    public function entreno()
    {
        return $this->hasMany(entrenos::class);
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
