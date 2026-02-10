<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;

class CrmSeeder extends Seeder
{
    public function run()
    {
        $seeders = [
            ChileLocationsSeeder::class,
            CustomerTypesSeeder::class,
            ProfessionalTypesSeeder::class,
            UrgencyTypesSeeder::class,
            CategoriesSeeder::class,
            SubscriptionPlansSeeder::class,
        ];

        if (env('APP_ENV') === 'local') {
            $seeders[] = SampleDataSeeder::class;
        }

        $this->call($seeders);
    }
}
