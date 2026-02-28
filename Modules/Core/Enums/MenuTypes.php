<?php

namespace Modules\Core\Enums;

enum MenuTypes: string
{
    case main = 0;
    case right_sidebar = 1;

    public function label(): string
    {
        return match ($this) {
            self::main => __('menu.types.main'),
            self::right_sidebar => __('menu.types.right_sidebar'),
        };
    }

    public static function options(): array
    {
        return array_map(fn ($menu_type) => [
            'value' => $menu_type->value,
            'text' => $menu_type->label(),
        ], self::cases());
    }

    public static function codes(): array
    {
        return array_map(fn ($menu_type) => $menu_type->value, self::cases());
    }
}
