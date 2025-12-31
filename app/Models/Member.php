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
        'location_id',
        'account_status_id',
        'is_active',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
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
