<?php

namespace Modules\Cmr\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Professional extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'professional_type_id',
        'first_name',
        'last_name',
        'email',
        'phone_e164',
        'dni_country_id',
        'dni',
        'average_rating',
        'bio',
    ];

    protected $casts = [
        'average_rating' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = strtolower($value);
    }

    public function professionalType(): BelongsTo
    {
        return $this->belongsTo(ProfessionalType::class);
    }

    public function dni_country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'dni_country_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(ProfessionalAddress::class);
    }

    public function subcategories(): BelongsToMany
    {
        return $this->belongsToMany(Subcategory::class, 'professional_subcategory');
    }

    public function scopeWithActiveSubscription($query)
    {
        return $query->whereHas('subscriptions', function ($q) {
            $q->where('status', 'active');
        });
    }

    public function scopeWithoutActiveSubscription($query)
    {
        return $query->whereDoesntHave('subscriptions', function ($q) {
            $q->where('status', 'active');
        });
    }

    public function scopePrioritized($query)
    {
        return $query
            ->orderByRaw("(EXISTS (SELECT 1 FROM subscriptions s WHERE s.professional_id = professionals.id AND s.status = 'active')) DESC")
            ->orderBy('average_rating', 'DESC');
    }
}

