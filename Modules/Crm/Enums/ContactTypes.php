<?php

namespace Modules\Crm\Enums;

enum ContactTypes: string
{
    case Email = 'email';
    case Phone = 'phone';
    case Whatsapp = 'whatsapp';

    public function label(): string
    {
        return match($this) {
            self::Email => __('contact_types.email'),
            self::Phone => __('contact_types.phone'),
            self::Whatsapp => __('contact_types.whatsapp'),
        };
    }

    public static function options(): array
    {
        return array_map(fn($contact_type) => [
            'value' => $contact_type->value,
            'text' => $contact_type->label(),
        ], self::cases());
    }

    public static function codes(): array
    {
        return array_map(fn($contact_type) => $contact_type->value, self::cases());
    }
}
