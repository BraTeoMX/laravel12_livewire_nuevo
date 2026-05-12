<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Intentar eliminar la FK si existe (sin doctrina/dbal necesario)
            try {
                $table->dropForeign(['role_id']);
            } catch (\Exception $e) {
                // Ignorar error si la FK no existe
            }
            
            // Recrear la FK correctamente (role_id es INT UNSIGNED, catalogo_roles.id es INT)
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
            $table->dropForeign(['role_id']);
        });
    }
};