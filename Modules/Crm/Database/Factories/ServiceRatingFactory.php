<?php

namespace Modules\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Crm\Models\Customer;
use Modules\Crm\Models\Service;
use Modules\Crm\Models\ServiceRating;

class ServiceRatingFactory extends Factory
{
    protected $model = ServiceRating::class;

    public function definition(): array
    {
        $service = Service::query()->inRandomOrder()->first();
        $customerId = $service?->customer_id ?? Customer::query()->inRandomOrder()->value('id');

        return [
            'service_id' => $service?->id,
            'customer_id' => $customerId,
            'csat' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->optional()->sentence(),
        ];
    }
}
