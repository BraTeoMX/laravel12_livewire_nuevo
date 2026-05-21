<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inspeccion extends Model
{
    use HasFactory;

    protected $table = 'inspecciones';

    protected $fillable = [
        'user_id',
        'maquina',
        'lote_intimark',
        'articulo',
        'proveedor',
        'color_nombre',
        'ancho_contratado_input',
        'ancho_contratado_cm',
        'material',
        'orden_compra',
        'numero_recepcion',
        'ancho_cortable',
        'numero_piezas',
        'numero_lote',
        'yarda_ticket',
        'yarda_actual',
        'observaciones',
    ];

    protected $casts = [
        'ancho_contratado_input' => 'decimal:2',
        'ancho_contratado_cm' => 'decimal:2',
        'ancho_cortable' => 'decimal:2',
        'numero_piezas' => 'integer',
        'yarda_ticket' => 'decimal:2',
        'yarda_actual' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function defectos(): HasMany
    {
        return $this->hasMany(InspeccionDefecto::class, 'inspeccion_id');
    }
}
