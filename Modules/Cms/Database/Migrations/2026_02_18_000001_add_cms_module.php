<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('modules')->insert([
            'name' => 'CMS',
            'slug' => 'cms',
            'description' => 'CMS module',
            'version' => '1.0.0',
            'is_active' => true,
        ]);
    }

    public function down(): void
    {
        DB::table('modules')->where('slug', 'cms')->delete();
    }
};
