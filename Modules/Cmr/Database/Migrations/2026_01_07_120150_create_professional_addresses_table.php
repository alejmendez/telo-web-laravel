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
            $table->foreignId('professional_id')->constrained('professionals')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('commune_id')->constrained('communes')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('line1', 200);
            $table->string('line2', 200)->nullable();
            $table->string('postal_code', 32)->nullable();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->index(['professional_id', 'effective_from']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professional_addresses');
    }
};
