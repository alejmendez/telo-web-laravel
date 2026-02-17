<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Crm\Enums\ContactTypes;

class Customer extends Model
{
    use HasFactory;

    protected $with = ['contacts'];

    protected $fillable = [
        'first_name',
        'last_name',
        'customer_type_id',
        'dni',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['email', 'phone_e164'];

    protected static function newFactory()
    {
        return \Modules\Crm\Database\Factories\CustomerFactory::new();
    }

    public function customerType(): BelongsTo
    {
        return $this->belongsTo(CustomerType::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
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

    public function getEmailAttribute()
    {
        return $this->contacts->where('contact_type', ContactTypes::Email->value)->pluck('content')->toArray();
    }

    public function getPhoneE164Attribute()
    {
        return $this->contacts->whereIn('contact_type', [ContactTypes::Phone->value, ContactTypes::Whatsapp->value])->pluck('content')->toArray();
    }
}
