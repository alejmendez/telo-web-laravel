<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();

            $table->text('description');
            $table->text('address');
            $table->string('status', 20);
            $table->integer('priority');
            $table->timestamp('sla_due_at');
            $table->timestamp('accepted_at')->nullable();

            $table->foreignId('customer_id')->constrained('customers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('urgency_type_id')->constrained('urgency_types')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('assigned_professional_id')->nullable()->constrained('professionals')->cascadeOnUpdate()->nullOnDelete();

            $table->index(['status']);
            $table->index(['customer_id']);
            $table->index(['assigned_professional_id']);

            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE requests ADD CONSTRAINT requests_status_check CHECK (status IN ('pending','active','rejected'))");
        DB::statement("CREATE UNIQUE INDEX requests_unique_active_customer ON requests (customer_id) WHERE status IN ('pending','active')");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS requests_unique_active_customer");
        DB::statement("ALTER TABLE requests DROP CONSTRAINT IF EXISTS requests_status_check");
        Schema::dropIfExists('requests');
    }
};
