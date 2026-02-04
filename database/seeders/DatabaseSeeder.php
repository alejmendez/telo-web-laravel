<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Users\Database\Seeders\RolSeeder;
use Modules\Users\Database\Seeders\UserSeeder;
use Modules\Crm\Database\Seeders\CrmSeeder;

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
            UserSeeder::class,
            CrmSeeder::class,
        ];

        $this->call($seeders);

        Artisan::call('app:sync-permissions');
    }
}
