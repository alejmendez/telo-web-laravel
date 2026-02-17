<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalContact extends Model
{
    use HasFactory;

    protected $fillable = ['professional_id', 'contact_type', 'content'];

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }
}
