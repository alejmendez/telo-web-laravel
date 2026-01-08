<?php

namespace Modules\Cmr\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cmr\Models\Commune;
use Modules\Cmr\Models\Customer;
use Modules\Cmr\Models\CustomerAddress;

class CustomerAddressFactory extends Factory
{
    protected $model = CustomerAddress::class;

    public function definition(): array
    {
        $customer = Customer::query()->inRandomOrder()->first();
        $commune = Commune::query()->inRandomOrder()->first();
        $from = $this->faker->dateTimeBetween('-2 years', '-1 month');
        $to = $this->faker->boolean(40) ? $this->faker->dateTimeBetween($from, 'now') : null;

        return [
            'customer_id' => $customer?->id,
            'commune_id' => $commune?->id,
            'line1' => $this->faker->streetAddress(),
            'line2' => $this->faker->optional()->secondaryAddress(),
            'postal_code' => $this->faker->optional()->postcode(),
            'effective_from' => $from,
            'effective_to' => $to,
            'is_primary' => $this->faker->boolean(30),
        ];
    }
}

