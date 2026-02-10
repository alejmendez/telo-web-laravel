<?php

namespace Modules\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Crm\Models\Location;
use Modules\Crm\Models\Professional;
use Modules\Crm\Models\ProfessionalType;

class ProfessionalFactory extends Factory
{
    protected $model = Professional::class;

    public function definition(): array
    {
        $type = ProfessionalType::query()->inRandomOrder()->first();
        if (!$type) {
            $type = ProfessionalType::create(['name' => 'Technician', 'code' => 'TECH']);
        }

        $location = Location::query()->inRandomOrder()->first();

        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $fullName = trim($firstName . ' ' . $lastName);
        $email = strtolower($firstName.'.'.$lastName.$this->faker->unique()->numberBetween(1, 100000).'@pro.example.com');

        return [
            'professional_type_id' => $type->id,
            'full_name' => $fullName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'dni' => $this->faker->unique()->numerify('##.###.###-#'),
            'average_rating' => $this->faker->randomFloat(2, 0, 5),
            'bio' => $this->faker->optional()->paragraph(),
        ];
    }
}
