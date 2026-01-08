<?php

namespace Modules\Cmr\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UrgencyType extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'name', 'priority_weight', 'sla_hours'];
}

