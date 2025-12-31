<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountStatus extends Model
{
    // Laravel espera la tabla 'account_statuses' por defecto
    protected $fillable = ['name', 'description'];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class, 'account_status_id');
    }
}