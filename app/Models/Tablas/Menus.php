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

    public function parent()
    {
        return $this->belongsTo(Menus::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menus::class, 'parent_id');
    }

    public function permisos()
    {
        return $this->hasMany(Permisos::class);
    }
}
