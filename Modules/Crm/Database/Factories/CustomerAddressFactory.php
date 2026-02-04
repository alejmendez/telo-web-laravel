<?php

namespace Modules\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Crm\Models\Location;
use Modules\Crm\Models\Customer;
use Modules\Crm\Models\CustomerAddress;

class CustomerAddressFactory extends Factory
{
    protected $model = CustomerAddress::class;

    public function definition(): array
    {
        $customer = Customer::query()->inRandomOrder()->first();
        $location = Location::query()->inRandomOrder()->first();
        $from = $this->faker->dateTimeBetween('-2 years', '-1 month');
        $to = $this->faker->boolean(40) ? $this->faker->dateTimeBetween($from, 'now') : null;

        return [
            'customer_id' => $customer?->id,
            'location_id' => $location?->id,
            'line1' => $this->faker->streetAddress(),
            'line2' => $this->faker->optional()->secondaryAddress(),
            'postal_code' => $this->faker->optional()->postcode(),
            'effective_from' => $from,
            'effective_to' => $to,
            'is_primary' => $this->faker->boolean(30),
        ];
    }
}
