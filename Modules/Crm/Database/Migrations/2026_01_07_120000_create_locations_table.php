<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('type'); // country, region, state, municipality, city, etc
            $table->string('country_code', 2)->index(); // CL, CO, PE, BR, VE

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete();

            $table->unsignedTinyInteger('level')->nullable(); // opcional
            $table->string('code')->nullable(); // código oficial si existe

            $table->timestamps();

            $table->index(['parent_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
