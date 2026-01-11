<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professionals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_type_id')->constrained('professional_types')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 200)->unique();
            $table->string('phone_e164', 32)->unique();
            $table->foreignId('dni_location_id')->constrained('locations')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('dni', 64);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->text('bio')->nullable();

            $table->unique(['dni_location_id', 'dni']);
            $table->index(['professional_type_id']);
            $table->index(['dni_location_id']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professionals');
    }
};
