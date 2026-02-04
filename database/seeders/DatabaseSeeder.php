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
use Modules\Crm\Database\Seeders\CategoriesSeeder;
use Modules\Crm\Database\Seeders\UrgencyTypesSeeder;
use Modules\Crm\Database\Seeders\CustomerSeeder;
use Modules\Crm\Database\Seeders\ProfessionalSeeder;
use Modules\Crm\Database\Seeders\RequestSeeder;

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
            CategoriesSeeder::class,
            UserSeeder::class,
            ChileLocationsSeeder::class,
            CustomerTypesSeeder::class,
            ContactTypesSeeder::class,
            ProfessionalTypesSeeder::class,
            UrgencyTypesSeeder::class,
        ];

        if (env('APP_ENV') === 'local') {
            $seeders[] = CustomerSeeder::class;
            $seeders[] = ProfessionalSeeder::class;
            $seeders[] = RequestSeeder::class;
        }

        $this->call($seeders);

        Artisan::call('app:sync-permissions');
    }
}
