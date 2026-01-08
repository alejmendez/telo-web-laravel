<?php

namespace Modules\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Crm\Models\Request;
use Modules\Crm\Models\Service;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $request = Request::query()->where('status', 'active')->inRandomOrder()->first();
        $started = $this->faker->dateTimeBetween('-2 days', 'now');
        $completed = $this->faker->boolean(40) ? $this->faker->dateTimeBetween($started, 'now') : null;
        $status = $completed ? 'rejected' : 'active';

        return [
            'request_id' => $request?->id,
            'customer_id' => $request?->customer_id,
            'professional_id' => $request?->assigned_professional_id,
            'status' => $status,
            'started_at' => $started,
            'completed_at' => $completed,
        ];
    }
}
