<?php

namespace Modules\Crm\Models;

use Modules\Crm\Enums\ContactTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Professional extends Model
{
    use HasFactory, SoftDeletes;

    protected $with = ['contacts'];

    protected $fillable = [
        'professional_type_id',
        'full_name',
        'first_name',
        'last_name',
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

    protected $appends = ['email', 'phone_e164'];

    protected static function newFactory()
    {
        return \Modules\Crm\Database\Factories\ProfessionalFactory::new();
    }

    public function professionalType(): BelongsTo
    {
        return $this->belongsTo(ProfessionalType::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
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

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'professional_category');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ProfessionalContact::class);
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


    public function getEmailAttribute()
    {
        return $this->contacts->where('contact_type', ContactTypes::Email->value)->pluck('content')->toArray();
    }

    public function getPhoneE164Attribute()
    {
        return $this->contacts->whereIn('contact_type', [ContactTypes::Phone->value, ContactTypes::Whatsapp->value])->pluck('content')->toArray();
    }
}
