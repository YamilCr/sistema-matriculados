<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Member extends Model
{
    protected $fillable = [
        'registration_number',
        'first_name',
        'last_name',
        'dni',
        'address',
        'phone',
        'city_id',
        'province_id',
        'account_status_id',
    ];

    /** Se incluyen al serializar para que el front pueda mostrar nombre y matrícula. */
    protected $appends = ['name', 'enrollment_number'];

    public function getNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function getEnrollmentNumberAttribute(): string
    {
        return (string) $this->registration_number;
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function accountStatus(): BelongsTo
    {
        return $this->belongsTo(AccountStatus::class, 'account_status_id');
    }

    // Un matriculado puede tener un usuario para entrar al sistema
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'member_id');
    }
}
