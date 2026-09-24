<?php

declare(strict_types=1);

namespace App\Enums;

enum MinistryDecision: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Keputusan Kemensos',
            self::Approved => 'Disetujui Kemensos',
            self::Rejected => 'Ditolak Kemensos',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }
}
