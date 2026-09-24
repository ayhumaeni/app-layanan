<?php

declare(strict_types=1);

namespace App\Enums;

enum PbiReactivationReason: string
{
    case Chronic = 'chronic';
    case Catastrophic = 'catastrophic';
    case Emergency = 'emergency';
    case Newborn = 'newborn';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Chronic => 'Penyakit Kronis',
            self::Catastrophic => 'Penyakit Katastropik',
            self::Emergency => 'Kondisi Darurat Medis',
            self::Newborn => 'Bayi Baru Lahir dari Ibu Peserta PBI',
            self::Other => 'Lainnya',
        };
    }

    public function isMedical(): bool
    {
        return in_array($this, [self::Chronic, self::Catastrophic, self::Emergency], true);
    }
}
