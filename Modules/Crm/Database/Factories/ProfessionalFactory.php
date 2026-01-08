<?php

namespace Modules\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Crm\Models\Country;
use Modules\Crm\Models\Professional;
use Modules\Crm\Models\ProfessionalType;

class ProfessionalFactory extends Factory
{
    protected $model = Professional::class;

    public function definition(): array
    {
        $country = Country::query()->inRandomOrder()->first();
        $type = ProfessionalType::query()->inRandomOrder()->first();
        $first = $this->faker->firstName();
        $last = $this->faker->lastName();
        $email = strtolower($first.'.'.$last.$this->faker->unique()->numberBetween(1, 100000).'@pro.example.com');

        return [
            'professional_type_id' => $type?->id,
            'first_name' => $first,
            'last_name' => $last,
            'email' => $email,
            'phone_e164' => $this->faker->unique()->e164PhoneNumber(),
            'dni_country_id' => $country?->id,
            'dni' => strtoupper($this->faker->bothify('########')),
            'average_rating' => 0,
            'bio' => $this->faker->optional()->paragraph(),
        ];
    }
}
