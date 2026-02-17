<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Model;

class UrgencyType extends Model
{
    protected $fillable = ['code', 'name', 'priority_weight', 'sla_hours'];
}
