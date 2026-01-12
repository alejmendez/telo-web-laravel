<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Users\Database\Seeders\RolSeeder;
use Modules\Users\Database\Seeders\UserSeeder;
use Modules\Crm\Database\Seeders\ChileLocationsSeeder;
use Modules\Crm\Database\Seeders\CustomerTypesSeeder;
use Modules\Crm\Database\Seeders\ContactTypesSeeder;
use Modules\Crm\Database\Seeders\ProfessionalTypesSeeder;
use Modules\Crm\Database\Seeders\CategoriesSubcategoriesSeeder;

use Modules\Users\Models\User;

use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (User::count() > 0) {
            return;
        }

        $seeders = [
            RolSeeder::class,
            CategoriesSubcategoriesSeeder::class,
            UserSeeder::class,
            ChileLocationsSeeder::class,
            CustomerTypesSeeder::class,
            ContactTypesSeeder::class,
            ProfessionalTypesSeeder::class,
        ];

        $this->call($seeders);

        Artisan::call('app:sync-permissions');
    }
}
