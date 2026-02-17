<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_type_id')->constrained('customer_types')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('full_name', 200);
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('dni', 64);
            $table->text('notes')->nullable();

            $table->unique(['dni']);
            $table->index(['customer_type_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
