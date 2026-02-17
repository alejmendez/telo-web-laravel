<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerContact extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'contact_type', 'content'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
