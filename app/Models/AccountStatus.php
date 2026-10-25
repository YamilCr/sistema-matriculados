<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Estados de cuenta FIJOS del sistema. No se crean ni se editan desde la app:
 * el matriculado se asigna a uno de estos mediante una lista desplegable.
 */
class AccountStatus extends Model
{
    public const AL_DIA = 'Al día';

    public const MOROSO = 'Moroso';

    public const SUSPENDIDO = 'Suspendido';

    /** Nombre => descripción. El orden define los ids (1, 2, 3). */
    public const FIXED = [
        self::AL_DIA => 'Sin deuda',
        self::MOROSO => 'Deuda mayor a 3 meses',
        self::SUSPENDIDO => 'Inhabilitado por sanción',
    ];

    protected $fillable = ['name', 'description'];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class, 'account_status_id');
    }
}
