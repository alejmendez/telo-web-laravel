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
            $table->foreignId('contact_type_id')->constrained('contact_types')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name', 120);
            $table->string('email', 200)->nullable();
            $table->string('phone_e164', 32)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("CREATE UNIQUE INDEX customer_contacts_unique_email ON customer_contacts (customer_id, email) WHERE email IS NOT NULL");
        DB::statement("CREATE UNIQUE INDEX customer_contacts_unique_phone ON customer_contacts (customer_id, phone_e164) WHERE phone_e164 IS NOT NULL");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS customer_contacts_unique_email");
        DB::statement("DROP INDEX IF EXISTS customer_contacts_unique_phone");
        Schema::dropIfExists('customer_contacts');
    }
};
