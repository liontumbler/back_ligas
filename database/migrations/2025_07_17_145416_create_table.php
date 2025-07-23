<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Lógica de funcionamiento
        Si un usuario entrena por sesión, se crea un pago y un entreno asociado al pago_id.
        Si tiene mensualidad activa, se crea el entreno con mensualidad_id y se incrementa sesiones_usadas.
        Si la mensualidad se venció o no quedan sesiones, se debe bloquear el entreno o notificar.
        Si no pagó, se puede registrar el entreno con estado = pendiente
     */
    public function up(): void
    {
        Schema::create('ligas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('direccion', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_modificacion')->nullable();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_modificacion')->nullable();
        });

        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('correo', 20)->unique();
            $table->string('password', 200);
            $table->foreignId('liga_id')->nullable()->constrained('ligas')->onDelete('cascade');
            $table->foreignId('rol_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('usuario_creacion')->nullable()->constrained('usuarios');
            $table->foreignId('usuario_modificacion')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_modificacion')->nullable();
        });

        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('correo', 20)->unique();
            $table->string('telefono', 20)->nullable();
            $table->foreignId('liga_id')->constrained('ligas')->onDelete('cascade');
            $table->foreignId('usuario_creacion')->nullable()->constrained('usuarios');
            $table->foreignId('usuario_modificacion')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_modificacion')->nullable();
        });

        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['mensualidad', 'sesion']);
            $table->decimal('valor', 10, 2);
            $table->dateTime('fecha_pago')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('estado', ['pagado', 'pendiente', 'vencido'])->default('pagado');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('liga_id')->constrained('ligas')->onDelete('cascade');
            $table->foreignId('usuario_creacion')->nullable()->constrained('usuarios');
            $table->foreignId('usuario_modificacion')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_modificacion')->nullable();
        });

        Schema::create('mensualidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->integer('sesiones_disponibles');
            $table->integer('sesiones_usadas')->default(0);
            $table->foreignId('liga_id')->constrained('ligas')->onDelete('cascade');
            $table->foreignId('usuario_creacion')->nullable()->constrained('usuarios');
            $table->foreignId('usuario_modificacion')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_modificacion')->nullable();
        });

        Schema::create('entrenos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->enum('tipo', ['individual', 'mensualidad', 'equipo']);
            $table->foreignId('pago_id')->nullable()->constrained('pagos')->onDelete('set null');
            $table->foreignId('mensualidad_id')->nullable()->constrained('mensualidades')->onDelete('set null');
            $table->foreignId('liga_id')->constrained('ligas')->onDelete('cascade');
            $table->foreignId('usuario_creacion')->nullable()->constrained('usuarios');
            $table->foreignId('usuario_modificacion')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_modificacion')->nullable();
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->foreignId('parent_id')->nullable()->constrained('menus')->onDelete('cascade');
            $table->foreignId('usuario_creacion')->nullable()->constrained('usuarios');
            $table->foreignId('usuario_modificacion')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_modificacion')->nullable();
        });

        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->enum('action', ['view', 'create', 'update', 'delete', 'read']);
            $table->foreignId('usuario_creacion')->nullable()->constrained('usuarios');
            $table->foreignId('usuario_modificacion')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_modificacion')->nullable();
        });

        Schema::create('permiso_rol', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permiso_id')->constrained('permisos')->onDelete('cascade');
            $table->foreignId('usuario_creacion')->nullable()->constrained('usuarios');
            $table->foreignId('usuario_modificacion')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_modificacion')->nullable();

            $table->unique(['rol_id', 'permiso_id']);
        });

        Schema::create('licencias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->decimal('valor', 10, 2);
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->enum('estado', ['activa', 'inactiva', 'vencida'])->default('activa');
            $table->foreignId('usuario_creacion')->nullable()->constrained('usuarios');
            $table->foreignId('usuario_modificacion')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_modificacion')->nullable();
        });

        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('refresh_token');
            $table->string('ip_address', 45);
            $table->string('usuario_agent');
            $table->string('pais', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);        
            $table->boolean('revoked')->default(false);
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_modificacion')->nullable();

            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');

            $table->index('refresh_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licencias');
        Schema::dropIfExists('entrenos');              
        Schema::dropIfExists('mensualidades');         
        Schema::dropIfExists('pagos');                 
        Schema::dropIfExists('clientes');              
        Schema::dropIfExists('permiso_rol');           
        Schema::dropIfExists('permisos');              
        Schema::dropIfExists('menus');  
        Schema::dropIfExists('refresh_tokens');                       
        Schema::dropIfExists('usuarios');  
        Schema::dropIfExists('ligas');           
        Schema::dropIfExists('roles');  
        
    }
};
