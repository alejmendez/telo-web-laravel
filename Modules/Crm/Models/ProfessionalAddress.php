<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfessionalAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'professional_id',
        'location_id',
        'line1',
        'line2',
        'postal_code',
        'effective_from',
        'effective_to',
        'is_primary',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_primary' => 'boolean',
    ];

    protected static function newFactory()
    {
        return \Modules\Crm\Database\Factories\ProfessionalAddressFactory::new();
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
