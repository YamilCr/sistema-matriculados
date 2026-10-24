<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Province extends Model
{
    protected $fillable = ['name'];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Alfabético ignorando tildes y mayúsculas (SQLite manda "Córdoba" al final).
     *
     * @param  Collection<int, Province>  $items
     * @return Collection<int, Province>
     */
    public static function sortedByName(Collection $items): Collection
    {
        return $items->sortBy(fn ($i) => Str::lower(Str::ascii($i->name)))->values();
    }
}
