<?php

namespace Modules\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Crm\Models\Customer;
use Modules\Crm\Models\CustomerAddress;
use Modules\Crm\Models\Location;

class CustomerAddressFactory extends Factory
{
    protected $model = CustomerAddress::class;

    public function definition(): array
    {
        $customer = Customer::query()->inRandomOrder()->first();
        $location = Location::query()->inRandomOrder()->first();

        return [
            'customer_id' => $customer?->id,
            'location_id' => $location?->id,
            'address' => $this->faker->streetAddress(),
            'postal_code' => $this->faker->optional()->postcode(),
            'is_primary' => $this->faker->boolean(30),
        ];
    }
}
