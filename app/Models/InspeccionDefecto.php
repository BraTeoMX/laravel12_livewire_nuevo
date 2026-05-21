<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspeccionDefecto extends Model
{
    use HasFactory;

    protected $table = 'inspeccion_defectos';

    protected $fillable = [
        'inspeccion_id',
        'defecto_id',
        'puntos',
        'cantidad',
        'puntos_calculados',
    ];

    protected $casts = [
        'puntos' => 'integer',
        'cantidad' => 'integer',
        'puntos_calculados' => 'integer',
    ];

    public function inspeccion(): BelongsTo
    {
        return $this->belongsTo(Inspeccion::class, 'inspeccion_id');
    }

    public function catalogoDefecto(): BelongsTo
    {
        return $this->belongsTo(CatalogoDefecto::class, 'defecto_id');
    }
}
