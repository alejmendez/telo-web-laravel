<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'subcategory_id',
        'urgency_type_id',
        'assigned_professional_id',
        'title',
        'description',
        'status',
        'priority',
        'sla_due_at',
        'accepted_at',
    ];

    protected $casts = [
        'priority' => 'integer',
        'sla_due_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function urgencyType(): BelongsTo
    {
        return $this->belongsTo(UrgencyType::class, 'urgency_type_id');
    }

    public function assignedProfessional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'assigned_professional_id');
    }

    public function service(): HasOne
    {
        return $this->hasOne(Service::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_professional_id');
    }

    public function scopeForCustomer($query, $id)
    {
        return $query->where('customer_id', $id);
    }
}
