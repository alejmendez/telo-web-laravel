<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Crm\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::factory()->create([
            'full_name' => 'Alejandro Méndez Customer',
            'first_name' => 'Alejandro',
            'last_name' => 'Méndez Customer',
            'dni' => '12.345.678-1',
            'email' => 'alejmendez.87@gmail.com',
            'phone_e164' => '+56968005128',
        ]);
        Customer::factory()->count(30)->create();
    }
}
