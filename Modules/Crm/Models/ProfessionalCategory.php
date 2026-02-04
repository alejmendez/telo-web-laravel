<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfessionalCategory extends Model
{
    use SoftDeletes;

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
