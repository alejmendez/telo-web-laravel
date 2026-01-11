<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'name',
        'type',
        'country_code',
        'parent_id',
        'level',
        'code',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // Helpers útiles
    public function scopeCountry($query, string $code)
    {
        return $query->where('country_code', $code);
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
