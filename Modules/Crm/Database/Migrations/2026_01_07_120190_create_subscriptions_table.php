<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained('professionals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('status', 20);
            $table->timestamp('active_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->index(['professional_id']);
            $table->index(['status']);

            $table->timestamps();
        });

        DB::statement("ALTER TABLE subscriptions ADD CONSTRAINT subscriptions_status_check CHECK (status IN ('pending','active','rejected'))");
        DB::statement("CREATE UNIQUE INDEX subscriptions_unique_active_professional ON subscriptions (professional_id) WHERE status = 'active'");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS subscriptions_unique_active_professional');
        DB::statement('ALTER TABLE subscriptions DROP CONSTRAINT IF EXISTS subscriptions_status_check');
        Schema::dropIfExists('subscriptions');
    }
};
