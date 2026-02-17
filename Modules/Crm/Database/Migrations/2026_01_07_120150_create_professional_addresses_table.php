<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professional_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained('professionals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('address', 250);
            $table->string('postal_code', 32)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->index(['professional_id', 'location_id', 'address']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professional_addresses');
    }
};
