<?php

declare(strict_types=1);

namespace App\Enums;

enum ReferralStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Accepted = 'accepted';
    case InService = 'in_service';
    case Completed = 'completed';
    case Declined = 'declined';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf Rujukan',
            self::Sent => 'Rujukan Dikirim',
            self::Accepted => 'Diterima Lembaga',
            self::InService => 'Dalam Pelayanan Lembaga',
            self::Completed => 'Selesai',
            self::Declined => 'Ditolak Lembaga',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Sent, self::Accepted => 'warning',
            self::InService => 'primary',
            self::Completed => 'success',
            self::Declined, self::Cancelled => 'danger',
        };
    }
}
