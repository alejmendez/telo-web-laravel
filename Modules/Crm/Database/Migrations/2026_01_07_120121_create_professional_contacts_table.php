<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professional_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained('professionals')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('contact_type', 32);
            $table->string('content', 200);

            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("CREATE UNIQUE INDEX professional_contacts_unique_content ON professional_contacts (professional_id, contact_type, content) WHERE content IS NOT NULL");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS professional_contacts_unique_content");
        Schema::dropIfExists('professional_contacts');
    }
};
