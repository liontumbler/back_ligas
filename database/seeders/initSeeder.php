<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class initSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['nombre' => 'Admin'],
            ['nombre' => 'trabajador'],
            ['nombre' => 'Cliente'],
        ]);

        DB::table('menus')->insert([
            ['id' => 1, 'nombre' => 'Usuarios', 'parent_id' => null],
            ['id' => 2, 'nombre' => 'Configuración', 'parent_id' => null],
            ['id' => 3, 'nombre' => 'Crear Usuario', 'parent_id' => 1],
            ['id' => 4, 'nombre' => 'Editar Usuario', 'parent_id' => 1],
            ['id' => 5, 'nombre' => 'Leer Usuario', 'parent_id' => 1],
            ['id' => 6, 'nombre' => 'Editar Usuario', 'parent_id' => 1],
        ]);

        DB::table('usuarios')->insert([
            [
                'nombres' => 'super',
                'apellidos' => 'Admin',
                'correo' => "lion_3214@hotmail.com",
                'password' => Hash::make('admin'),
                'rol_id' => 1
            ],
            [
                'nombre' => 'Edwin',
                'apellidos' => 'Velasquez',
                'correo' => 'edwin@mail.com',
                'password' => Hash::make('123456'),
                'rol_id' => 2
            ],
            [
                'nombre' => 'Mauricio',
                'apellidos' => 'Ocampo',
                'correo' => 'ivancho88_8@hotmail.com',
                'password' => Hash::make('123456'),
                'rol_id' => 1
            ],
            [
                'nombre' => 'Perez',
                'apellidos' => 'Ppito',
                'correo' => 'ppitoape@hotmail.com',
                'password' => Hash::make('123456'),
                'rol_id' => 3
            ]
        ]);

        DB::table('licencias')->insert([
            'codigo' => 'superAdminLicense',
            'valor' => 0,
            'fecha_inicio' => "2025-07-01 00:00:00",
            'estado' => 'activa',
            'usuario_creacion' => 1,
            'usuario_modificacion' => 1,
        ]);

        DB::table('permisos')->insert([
            ['id' => 1, 'menu_id' => 1, 'action' => 'view'],     // Usuarios
            ['id' => 2, 'menu_id' => 3, 'action' => 'create'],   // Crear Usuario
            ['id' => 3, 'menu_id' => 4, 'action' => 'update'],   // Editar Usuario
            ['id' => 4, 'menu_id' => 2, 'action' => 'view'],     // Configuración
        ]);

        DB::table('permiso_rol')->insert([
            ['rol_id' => 1, 'permiso_id' => 1],
            ['rol_id' => 1, 'permiso_id' => 2],
            ['rol_id' => 1, 'permiso_id' => 3],
            ['rol_id' => 1, 'permiso_id' => 4],
            ['rol_id' => 2, 'permiso_id' => 1],
            ['rol_id' => 2, 'permiso_id' => 3],
        ]);

        DB::statement("SELECT setval('menus_id_seq', (SELECT MAX(id) FROM menus))");
        DB::statement("SELECT setval('permisos_id_seq', (SELECT MAX(id) FROM permisos))");
    }
}
