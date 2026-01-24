<?php

namespace Modules\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Crm\Models\Customer;
use Modules\Crm\Models\CustomerType;
use Modules\Crm\Models\Location;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $type = CustomerType::query()->inRandomOrder()->first();
        if (!$type) {
             $type = CustomerType::create(['name' => 'Regular', 'code' => 'REG']);
        }

        $location = Location::query()->inRandomOrder()->first();
        if (!$location) {
            $location = Location::create(['name' => 'New York', 'type' => 'city', 'country_code' => 'US']);
        }

        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $fullName = trim($firstName . ' ' . $lastName);

        return [
            'customer_type_id' => $type->id,
            'full_name' => $fullName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $this->faker->unique()->safeEmail(),
            'phone_e164' => $this->faker->unique()->e164PhoneNumber(),
            'location_id' => $location->id,
            'dni' => $this->faker->unique()->numerify('########'),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
