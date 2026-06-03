<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('catalogo_roles')) {
            Schema::create('catalogo_roles', function (Blueprint $table) {
                $table->increments('id'); // Genera un INT UNSIGNED AUTO_INCREMENT
                $table->string('nombre');
                $table->string('descripcion')->nullable();
                $table->timestamps();
            });

            // Sembrar los roles por defecto requeridos para el funcionamiento del sistema y pruebas
            DB::table('catalogo_roles')->insert([
                [
                    'id' => 1,
                    'nombre' => 'Administrador',
                    'descripcion' => 'uso total y absoluto del sistema, este rol es para los desarrolladores del sistema, no se debe uar para otro mas',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 2,
                    'nombre' => 'Gerente',
                    'descripcion' => 'contron total del sistema',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 3,
                    'nombre' => 'Gestion',
                    'descripcion' => 'enfocado si solo se requiere para manejo de algunas opciones como altas, bajas, etc',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 4,
                    'nombre' => 'Consulta',
                    'descripcion' => 'ver reportes y consultar datos del sistema',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 5,
                    'nombre' => 'Auditor',
                    'descripcion' => 'formularios',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogo_roles');
    }
};
