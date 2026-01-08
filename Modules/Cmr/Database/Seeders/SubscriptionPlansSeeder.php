<?php

namespace Modules\Cmr\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Cmr\Models\SubscriptionPlan;

class SubscriptionPlansSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['code' => 'trial', 'name' => 'Trial', 'duration_months' => 3, 'price_cents' => 0, 'currency' => 'USD', 'is_trial' => true],
            ['code' => 'standard', 'name' => 'Standard', 'duration_months' => 12, 'price_cents' => 19900, 'currency' => 'USD', 'is_trial' => false],
            ['code' => 'premium', 'name' => 'Premium', 'duration_months' => 12, 'price_cents' => 39900, 'currency' => 'USD', 'is_trial' => false],
        ];

        foreach ($data as $item) {
            SubscriptionPlan::query()->updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'duration_months' => $item['duration_months'],
                    'price_cents' => $item['price_cents'],
                    'currency' => $item['currency'],
                    'is_trial' => $item['is_trial'],
                ]
            );
        }
    }
}

