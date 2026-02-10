<?php

namespace Modules\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Crm\Models\Location;
use Modules\Crm\Models\Professional;
use Modules\Crm\Models\ProfessionalAddress;

class ProfessionalAddressFactory extends Factory
{
    protected $model = ProfessionalAddress::class;

    public function definition(): array
    {
        $professional = Professional::query()->inRandomOrder()->first();
        $location = Location::query()->inRandomOrder()->first();

        return [
            'professional_id' => $professional?->id,
            'location_id' => $location?->id,
            'address' => $this->faker->streetAddress(),
            'postal_code' => $this->faker->optional()->postcode(),
            'is_primary' => $this->faker->boolean(30),
        ];
    }
}
