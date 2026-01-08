<?php

namespace Modules\Cmr\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Cmr\Models\CustomerType;

class CustomerTypesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['code' => 'home_owner', 'name' => 'Home Owner'],
            ['code' => 'business_owner', 'name' => 'Business Owner'],
            ['code' => 'company', 'name' => 'Company'],
        ];

        foreach ($data as $item) {
            CustomerType::query()->updateOrCreate(
                ['code' => $item['code']],
                ['name' => $item['name']]
            );
        }
    }
}

