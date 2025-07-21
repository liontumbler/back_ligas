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
        DB::table('licencias')->insert([
            'codigo' => 'superAdminLicense',
            'valor' => 0,
            'fecha_inicio' => "2025-07-01 00:00:00",
            'estado' => 'activa',
        ]);

        DB::table('usuarios')->insert([
            'nombres' => 'super',
            'apellidos' => 'Admin',
            'correo' => "admin@superadmin.com",
            'password' => Hash::make('Admin1234$'),
        ]);
    }
}
