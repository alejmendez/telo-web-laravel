<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->cascadeOnUpdate()->cascadeOnDelete()->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('professional_id')->constrained('professionals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('status', 20);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->index(['professional_id']);
            $table->index(['customer_id']);
            $table->index(['status']);

            $table->timestamps();
        });

        DB::statement("ALTER TABLE services ADD CONSTRAINT services_status_check CHECK (status IN ('pending','active','rejected'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE services DROP CONSTRAINT IF EXISTS services_status_check');
        Schema::dropIfExists('services');
    }
};
