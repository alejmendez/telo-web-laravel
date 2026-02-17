<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('contact_type', 32);
            $table->string('content', 200);

            $table->timestamps();
        });

        DB::statement("CREATE UNIQUE INDEX customer_contacts_unique_content ON customer_contacts (customer_id, contact_type, content) WHERE content IS NOT NULL");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS customer_contacts_unique_content");
        Schema::dropIfExists('customer_contacts');
    }
};
