<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ServiceRating extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'customer_id', 'csat', 'comment'];

    protected $casts = [
        'csat' => 'integer',
    ];

    protected static function newFactory()
    {
        return \Modules\Crm\Database\Factories\ServiceRatingFactory::new();
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    protected static function booted()
    {
        static::saved(function (ServiceRating $rating) {
            $professionalId = $rating->service()->value('professional_id');
            if ($professionalId) {
                $avg = DB::table('service_ratings')
                    ->join('services', 'service_ratings.service_id', '=', 'services.id')
                    ->where('services.professional_id', $professionalId)
                    ->avg('service_ratings.csat');

                Professional::query()->where('id', $professionalId)->update([
                    'average_rating' => round($avg ?? 0, 2),
                ]);
            }
        });
    }
}
