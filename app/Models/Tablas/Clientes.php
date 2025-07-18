<?php

namespace App\Models\Tablas;

use Illuminate\Database\Eloquent\Model;

use App\Models\Tablas\Pagos;
use App\Models\Tablas\Mensualidades;
use App\Models\Tablas\Entrenos;

class Clientes extends Model
{
    const CREATED_AT        =   'fecha_creacion';
    const UPDATED_AT        =   'fecha_modificacion';

    protected $table        =   'clientes';
    protected $primaryKey   =   'id';

    public $incrementing    =   true;
    public $timestamps      =   true;

    protected $fillable = ['nombre', 'email', 'telefono'];

    public function pagos()
    {
        return $this->hasMany(Pagos::class);
    }
    public function mensualidades()
    {
        return $this->hasMany(Mensualidades::class);
    }
    public function entrenos()
    {
        return $this->hasMany(Entrenos::class);
    }
}
