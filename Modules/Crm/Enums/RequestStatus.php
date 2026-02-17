<?php

namespace Modules\Crm\Enums;

enum RequestStatus: string
{
    case Abandoned = 'abandoned';
    case Assigned = 'assigned';
    case Completed = 'completed';
    case InProcess = 'in_process';
    case Pending = 'pending';
    case Reassigned = 'reassigned';
    case Rejected = 'rejected';
    case ToBeContacted = 'to_be_contacted';
    case Unassigned = 'unassigned';
    case WaitingResponse = 'waiting_response';

    public function label(): string
    {
        return match ($this) {
            self::Abandoned => __('statuses.abandoned'),
            self::Assigned => __('statuses.assigned'),
            self::Completed => __('statuses.completed'),
            self::InProcess => __('statuses.in_process'),
            self::Pending => __('statuses.pending'),
            self::Reassigned => __('statuses.reassigned'),
            self::Rejected => __('statuses.rejected'),
            self::ToBeContacted => __('statuses.to_be_contacted'),
            self::Unassigned => __('statuses.unassigned'),
            self::WaitingResponse => __('statuses.waiting_response'),
        };
    }

    public static function options(): array
    {
        return array_map(fn ($status) => [
            'value' => $status->value,
            'text' => $status->label(),
        ], self::cases());
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
