<?php

namespace Modules\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Crm\Models\Country;
use Modules\Crm\Models\Customer;
use Modules\Crm\Models\CustomerType;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $country = Country::query()->inRandomOrder()->first();
        $type = CustomerType::query()->inRandomOrder()->first();
        $first = $this->faker->firstName();
        $last = $this->faker->lastName();
        $email = strtolower($first.'.'.$last.$this->faker->unique()->numberBetween(1, 100000).'@example.com');

        return [
            'customer_type_id' => $type?->id,
            'first_name' => $first,
            'last_name' => $last,
            'email' => $email,
            'phone_e164' => $this->faker->unique()->e164PhoneNumber(),
            'dni_country_id' => $country?->id,
            'dni' => strtoupper($this->faker->bothify('########')),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
