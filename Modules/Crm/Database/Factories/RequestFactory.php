<?php

namespace Modules\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Crm\Models\Customer;
use Modules\Crm\Models\Professional;
use Modules\Crm\Models\Request;
use Modules\Crm\Models\Category;
use Modules\Crm\Models\UrgencyType;

class RequestFactory extends Factory
{
    protected $model = Request::class;

    public function definition(): array
    {
        $customer = Customer::factory()->create();

        $category = Category::query()->inRandomOrder()->first();
        if (!$category) {
            $category = \Modules\Crm\Models\Category::query()->inRandomOrder()->first() ?? \Modules\Crm\Models\Category::factory()->create();
            $category = Category::factory()->create(['category_id' => $category->id]);
        }

        $urgency = UrgencyType::query()->inRandomOrder()->first();
        if (!$urgency) {
             $urgency = UrgencyType::create(['code' => 'medium', 'name' => 'Medium', 'priority_weight' => 2, 'sla_hours' => 48]);
        }

        $status = $this->faker->randomElement(['pending', 'active', 'rejected']);
        $priority = $urgency->priority_weight;
        $slaDue = now()->addHours($urgency->sla_hours);

        $assigned = null;
        if ($this->faker->boolean(50)) {
             $professional = Professional::query()->inRandomOrder()->first();
             if ($professional) {
                 $assigned = $professional->id;
             }
        }

        $acceptedAt = $status === 'active' ? $this->faker->dateTimeBetween('-3 days', 'now') : null;

        return [
            'customer_id' => $customer->id,
            'category_id' => $category->id,
            'urgency_type_id' => $urgency->id,
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
