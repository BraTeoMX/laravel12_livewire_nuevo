<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogoRole extends Model
{
    protected $table = 'catalogo_roles';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the users that belong to this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
