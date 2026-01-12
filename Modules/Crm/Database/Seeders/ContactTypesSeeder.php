<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Crm\Models\ContactType;

class ContactTypesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['code' => 'phone', 'name' => 'Teléfono'],
            ['code' => 'email', 'name' => 'Correo Electrónico'],
            ['code' => 'whatsapp', 'name' => 'WhatsApp'],
        ];

        foreach ($data as $item) {
            ContactType::query()->updateOrCreate(
                ['code' => $item['code']],
                ['name' => $item['name']]
            );
        }
    }
}
