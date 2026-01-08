<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;

class CrmSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CustomerTypesSeeder::class,
            ProfessionalTypesSeeder::class,
            UrgencyTypesSeeder::class,
            CountriesCitiesCommunesSeeder::class,
            CategoriesSubcategoriesSeeder::class,
            SubscriptionPlansSeeder::class,
            StatusCatalogSeeder::class,
            SampleDataSeeder::class,
        ]);
    }
}
