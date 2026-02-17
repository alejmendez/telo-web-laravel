<?php

namespace Modules\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Crm\Enums\ContactTypes;
use Modules\Crm\Models\ProfessionalContact;

class ProfessionalContactFactory extends Factory
{
    protected $model = ProfessionalContact::class;

    public function definition(): array
    {
        $contactType = $this->faker->randomElement(ContactTypes::codes());

        $content = match ($contactType) {
            'email' => $this->faker->unique()->safeEmail(),
            'phone', 'whatsapp' => '+'.$this->faker->numberBetween(1, 9).$this->faker->numerify('##########'),
            default => $this->faker->text(20),
        };

        return [
            'contact_type' => $contactType,
            'content' => $content,
        ];
    }
}
