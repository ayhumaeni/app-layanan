<?php

declare(strict_types=1);

namespace App\Enums;

enum ApprovalDecision: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Returned = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Keputusan',
            self::Approved => 'Disetujui',
            self::Returned => 'Dikembalikan / Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Returned => 'danger',
        };
    }
}
