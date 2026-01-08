<?php

namespace Modules\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Crm\Models\Customer;
use Modules\Crm\Models\Professional;
use Modules\Crm\Models\Request;
use Modules\Crm\Models\Subcategory;
use Modules\Crm\Models\UrgencyType;

class RequestFactory extends Factory
{
    protected $model = Request::class;

    public function definition(): array
    {
        $customer = Customer::query()->inRandomOrder()->first();
        $subcategory = Subcategory::query()->inRandomOrder()->first();
        $urgency = UrgencyType::query()->inRandomOrder()->first();
        $status = $this->faker->randomElement(['pending', 'active', 'rejected']);
        $priority = $urgency ? $urgency->priority_weight : 1;
        $slaDue = now()->addHours($urgency ? $urgency->sla_hours : 24);
        $assigned = $this->faker->boolean(50) ? Professional::query()->inRandomOrder()->value('id') : null;
        $acceptedAt = $status === 'active' ? $this->faker->dateTimeBetween('-3 days', 'now') : null;

        return [
            'customer_id' => $customer?->id,
            'subcategory_id' => $subcategory?->id,
            'urgency_type_id' => $urgency?->id,
            'assigned_professional_id' => $assigned,
            'title' => $this->faker->sentence(6),
            'description' => $this->faker->paragraph(),
            'status' => $status,
            'priority' => $priority,
            'sla_due_at' => $slaDue,
            'accepted_at' => $acceptedAt,
        ];
    }
}
