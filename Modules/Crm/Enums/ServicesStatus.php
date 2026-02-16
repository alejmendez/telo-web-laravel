<?php

namespace Modules\Crm\Enums;

enum ServicesStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::Pending => __('statuses.pending'),
            self::Assigned => __('statuses.assigned'),
            self::Rejected => __('statuses.rejected'),
        };
    }

    public static function options(): array
    {
        return array_map(fn($status) => [
            'value' => $status->value,
            'text' => $status->label(),
        ], self::cases());
    }
}
