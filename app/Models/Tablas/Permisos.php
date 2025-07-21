<?php

namespace App\Models\Tablas;

use Illuminate\Database\Eloquent\Model;

use App\Models\Tablas\Roles;

class Permisos extends Model
{
    const CREATED_AT        =   'fecha_creacion';
    const UPDATED_AT        =   'fecha_modificacion';

    protected $table        =   'permisos';
    protected $primaryKey   =   'id';

    public $incrementing    =   true;
    public $timestamps      =   true;

    protected $fillable = [
        'menu_id',
        'action',
    ];

    public function menu()
    {
        return $this->belongsTo(Menus::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Roles::class, 'permiso_rol', 'permission_id', 'role_id')
                    ->withTimestamps();
    }
}
