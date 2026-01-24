<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Crm\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::factory()->count(30)->create();
    }
}
