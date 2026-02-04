<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Crm\Models\Professional;

class ProfessionalSeeder extends Seeder
{
    public function run(): void
    {
        Professional::factory()->create([
            'full_name' => 'Alejandro Méndez Professional',
            'first_name' => 'Alejandro',
            'last_name' => 'Méndez Professional',
            'dni' => '12.345.678-1',
            'email' => 'alejmendez.87@gmail.com',
            'phone_e164' => '+56968005128',
        ]);
        Professional::factory()->count(30)->create();
    }
}
