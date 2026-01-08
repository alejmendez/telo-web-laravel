<?php

namespace Modules\Cmr\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfessionalSubcategory extends Model
{
    use SoftDeletes;

    protected $table = 'professional_subcategory';

    protected $fillable = ['professional_id', 'subcategory_id'];

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }
}

