<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class City extends Model
{
    protected $fillable = ['name', 'province_id'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Alfabético ignorando tildes y mayúsculas (SQLite manda "Córdoba" al final).
     *
     * @param  Collection<int, City>  $items
     * @return Collection<int, City>
     */
    public static function sortedByName(Collection $items): Collection
    {
        return $items->sortBy(fn ($i) => Str::lower(Str::ascii($i->name)))->values();
    }
}
