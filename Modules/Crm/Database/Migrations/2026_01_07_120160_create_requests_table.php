<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

            $table->foreignId('customer_id')->constrained('customers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('urgency_type_id')->constrained('urgency_types')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('professional_id')->nullable()->constrained('professionals')->cascadeOnUpdate()->nullOnDelete();

            $table->index(['status']);
            $table->index(['customer_id']);
            $table->index(['professional_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
