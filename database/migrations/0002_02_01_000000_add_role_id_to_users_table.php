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
        // 1. Asegurar que catalogo_roles.id sea UNSIGNED (compatible con INT UNSIGNED)
        DB::unprepared('ALTER TABLE catalogo_roles MODIFY COLUMN id INT UNSIGNED NOT NULL AUTO_INCREMENT;');

        // 2. Si la columna role_id ya existe, eliminar la FK conflictiva si existe
        if (Schema::hasColumn('users', 'role_id')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropForeign(['role_id']);
                });
            } catch (\Exception $e) {
                // FK no existía, ignorar
            }
        } else {
            // Si no existe la columna, crearla
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('role_id')->nullable()->after('id');
            });
        }

        // 3. Crear la FK correcta
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('role_id')
                  ->references('id')
                  ->on('catalogo_roles')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            try {
                $table->dropForeign(['role_id']);
            } catch (\Exception $e) {
                // ignorar si no existe
            }
            $table->dropColumn('role_id');
        });

        DB::unprepared('ALTER TABLE catalogo_roles MODIFY COLUMN id INT NOT NULL AUTO_INCREMENT;');
    }
};