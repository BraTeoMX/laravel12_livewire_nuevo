<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspeccionTelaTemporal extends Model
{
    protected $table = 'inspeccion_tela_temporales';

    protected $fillable = [
        'source_key',
        'numero_diario',
        'orden_compra',
        'proveedor',
        'estilo',
        'nombre_producto',
        'nombre_producto_externo',
        'estilo_externo',
        'talla',
        'color',
        'articulo',
        'ancho_contratado',
        'lote_intimark',
        'termino_busqueda',
    ];

    protected $casts = [
        'ancho_contratado' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
