<?php

declare(strict_types=1);

namespace App\Enums;

enum HandlingType: string
{
    case Direct = 'direct';
    case Referral = 'referral';
    case Both = 'both';

    public function label(): string
    {
        return match ($this) {
            self::Direct => 'Pelayanan Langsung',
            self::Referral => 'Rujukan ke Lembaga',
            self::Both => 'Pelayanan Langsung & Rujukan',
        };
    }
}
