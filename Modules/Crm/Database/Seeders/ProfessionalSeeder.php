<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Crm\Models\Professional;

class ProfessionalSeeder extends Seeder
{
    public function run(): void
    {
        Professional::factory()->count(30)->create();
    }
}
