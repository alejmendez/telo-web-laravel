<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Crm\Models\Request;

class RequestSeeder extends Seeder
{
    public function run(): void
    {
        Request::factory()->count(30)->create();
    }
}
