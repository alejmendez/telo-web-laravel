<?php

namespace Modules\Cmr\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfessionalType extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'name'];
}

