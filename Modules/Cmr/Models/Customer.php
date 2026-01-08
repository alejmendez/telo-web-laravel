<?php

namespace Modules\Cmr\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_type_id',
        'first_name',
        'last_name',
        'email',
        'phone_e164',
        'dni_country_id',
        'dni',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = strtolower($value);
    }

    public function customerType(): BelongsTo
    {
        return $this->belongsTo(CustomerType::class);
    }

    public function dni_country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'dni_country_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(CustomerContact::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function serviceRatings(): HasMany
    {
        return $this->hasMany(ServiceRating::class);
    }

    public function scopeWithActiveRequest($query)
    {
        return $query->whereHas('requests', function ($q) {
            $q->whereIn('status', ['pending', 'active']);
        });
    }

    public function scopeWithoutActiveRequest($query)
    {
        return $query->whereDoesntHave('requests', function ($q) {
            $q->whereIn('status', ['pending', 'active']);
        });
    }
}

