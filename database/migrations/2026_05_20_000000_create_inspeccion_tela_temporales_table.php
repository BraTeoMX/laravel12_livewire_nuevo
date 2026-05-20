<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('inspeccion_tela_temporales');

        Schema::create('inspeccion_tela_temporales', function (Blueprint $table) {
            $table->id();
            $table->char('source_key', 64)->unique();
            $table->string('numero_diario', 80)->nullable()->index();
            $table->string('orden_compra', 80)->nullable()->index();
            $table->string('proveedor')->nullable();
            $table->string('estilo', 120)->nullable();
            $table->string('nombre_producto')->nullable();
            $table->string('nombre_producto_externo')->nullable();
            $table->string('estilo_externo', 120)->nullable();
            $table->string('talla', 80)->nullable();
            $table->string('color', 80)->nullable();
            $table->string('articulo', 160)->nullable();
            $table->unsignedSmallInteger('ancho_contratado')->nullable();
            $table->string('lote_intimark', 120)->nullable()->index();
            $table->string('termino_busqueda', 80)->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspeccion_tela_temporales');
    }
};
