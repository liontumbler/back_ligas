<?php

namespace App\Models\Tablas;

use Illuminate\Database\Eloquent\Model;

class RefreshTokens extends Model
{
    const CREATED_AT        =   'fecha_creacion';
    const UPDATED_AT        =   'fecha_modificacion';

    protected $table        =   'refresh_tokens';
    protected $primaryKey   =   'id';

    public $incrementing    =   true;
    public $timestamps      =   true;

    protected $fillable = [
        'continente',
        'pais',
        'ciudad',
        'latitud',
        'longitud',
        'usuario_id',
        'refresh_token',
        'ip_address',
        'usuario_agent',
        'revoked',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'usuario_id');
    }

    public function isExpired()
    {
        return $this->fecha_creacion->addDays(7)->isPast();
    }
}
