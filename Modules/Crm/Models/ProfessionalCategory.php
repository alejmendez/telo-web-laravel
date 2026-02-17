<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalCategory extends Model
{
    protected $table = 'professional_categories';

    protected $fillable = ['professional_id', 'category_id'];

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
