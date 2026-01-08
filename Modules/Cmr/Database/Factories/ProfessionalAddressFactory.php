<?php

namespace Modules\Cmr\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cmr\Models\Commune;
use Modules\Cmr\Models\Professional;
use Modules\Cmr\Models\ProfessionalAddress;

class ProfessionalAddressFactory extends Factory
{
    protected $model = ProfessionalAddress::class;

    public function definition(): array
    {
        $professional = Professional::query()->inRandomOrder()->first();
        $commune = Commune::query()->inRandomOrder()->first();
        $from = $this->faker->dateTimeBetween('-2 years', '-1 month');
        $to = $this->faker->boolean(40) ? $this->faker->dateTimeBetween($from, 'now') : null;

        return [
            'professional_id' => $professional?->id,
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
