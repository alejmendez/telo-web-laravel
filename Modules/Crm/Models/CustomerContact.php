<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerContact extends Model
{
    use SoftDeletes;

    protected $fillable = ['customer_id', 'contact_type_id', 'name', 'email', 'phone_e164'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function contactType(): BelongsTo
    {
        return $this->belongsTo(ContactType::class);
    }
}
