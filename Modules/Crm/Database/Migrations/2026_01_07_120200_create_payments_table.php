<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained('professionals')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->cascadeOnUpdate()->nullOnDelete();
            $table->bigInteger('amount_cents');
            $table->string('currency', 8);
            $table->string('status', 20);
            $table->timestamp('paid_at')->nullable();
            $table->string('external_ref', 200)->nullable();

            $table->index(['professional_id']);
            $table->index(['subscription_id']);
            $table->index(['paid_at']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
