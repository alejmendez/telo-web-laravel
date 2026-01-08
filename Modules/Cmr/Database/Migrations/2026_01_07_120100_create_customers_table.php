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
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 200)->unique();
            $table->string('phone_e164', 32)->unique();
            $table->foreignId('dni_country_id')->constrained('countries')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('dni', 64);
            $table->text('notes')->nullable();

            $table->unique(['dni_country_id', 'dni']);
            $table->index(['customer_type_id']);
            $table->index(['dni_country_id']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
