<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspecciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('maquina')->nullable();
            $table->string('lote_intimark')->index();
            $table->string('articulo');
            $table->string('proveedor')->nullable();
            $table->string('color_nombre')->nullable();
            $table->decimal('ancho_contratado_input', 8, 2)->nullable();
            $table->decimal('ancho_contratado_cm', 8, 2)->nullable();
            $table->string('material')->nullable();
            $table->string('orden_compra')->index();
            $table->string('numero_recepcion')->index();
            $table->decimal('ancho_cortable', 8, 2);
            $table->integer('numero_piezas');
            $table->string('numero_lote'); // lote teñido
            $table->decimal('yarda_ticket', 8, 2);
            $table->decimal('yarda_actual', 8, 2);
            $table->text('observaciones')->nullable();
            $table->integer('total_puntos_defectos')->default(0)->comment('Suma total de (cantidad * puntos) de todos sus defectos');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspecciones');
    }
};
