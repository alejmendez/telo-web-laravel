<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'category_id',
        'urgency_type_id',
        'professional_id',
        'address',
        'description',
        'status',
        'priority',
        'sla_due_at',
        'accepted_at',
    ];

    protected $casts = [
        'status' => \Modules\Crm\Enums\RequestStatus::class,
        'priority' => 'integer',
        'sla_due_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function (self $request) {
            $request->storeHistory('created');
        });

        static::updated(function (self $request) {
            $request->storeHistory('updated');
        });

        static::deleted(function (self $request) {
            $request->storeHistory('deleted');
        });
    }

    protected static function newFactory()
    {
        return \Modules\Crm\Database\Factories\RequestFactory::new();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function urgency_type(): BelongsTo
    {
        return $this->belongsTo(UrgencyType::class, 'urgency_type_id');
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'professional_id');
    }

    public function service(): HasOne
    {
        return $this->hasOne(Service::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(RequestHistory::class);
    }

    public function scopeForCustomer($query, $id)
    {
        return $query->where('customer_id', $id);
    }

    public function storeHistory(string $operation): void
    {
        $this->histories()->create([
            'operation' => $operation,
            'data' => $this->getAttributes(),
        ]);
    }
}
