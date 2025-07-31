<?php

namespace App\Models\Tablas;

use Illuminate\Database\Eloquent\Model;

class Menus extends Model
{
    const CREATED_AT        =   'fecha_creacion';
    const UPDATED_AT        =   'fecha_modificacion';

    protected $table        =   'menus';
    protected $primaryKey   =   'id';

    public $incrementing    =   true;
    public $timestamps      =   true;

    protected $fillable = [
        'name',
        'parent_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function permisos(): HasMany
    {
        return $this->hasMany(Permiso::class);
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
