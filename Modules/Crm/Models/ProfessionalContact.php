<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfessionalContact extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = ['professional_id', 'contact_type', 'content'];

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }
}
