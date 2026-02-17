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
            $table->string('dni', 64);
            $table->string('full_name', 200);
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->text('bio')->nullable();

            $table->foreignId('professional_type_id')->constrained('professional_types')->cascadeOnUpdate()->restrictOnDelete();

            $table->unique(['dni']);
            $table->index(['professional_type_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professionals');
    }
};
