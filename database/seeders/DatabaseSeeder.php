<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Users\Database\Seeders\RolSeeder;
use Modules\Users\Database\Seeders\UserSeeder;
use Modules\Crm\Database\Seeders\ChileLocationsSeeder;
use Modules\Users\Models\User;

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
            ChileLocationsSeeder::class,
        ];

        if (app()->isLocal()) {
            $seeders = [
                RolSeeder::class,
                UserSeeder::class,
                ChileLocationsSeeder::class,
            ];
        }
        $this->call($seeders);
    }
}
