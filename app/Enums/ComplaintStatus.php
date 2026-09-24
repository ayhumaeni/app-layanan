<?php

declare(strict_types=1);

namespace App\Enums;

enum ComplaintStatus: string
{
    case Received = 'received';
    case Verification = 'verification';
    case ClarificationRequested = 'clarification_requested';
    case Dispatched = 'dispatched';
    case InHandling = 'in_handling';
    case Resolved = 'resolved';
    case Duplicate = 'duplicate';
    case Invalid = 'invalid';

    public function label(): string
    {
        return match ($this) {
            self::Received => 'Pengaduan Diterima',
            self::Verification => 'Verifikasi Awal',
            self::ClarificationRequested => 'Perlu Klarifikasi Pelapor',
            self::Dispatched => 'Didisposisikan',
            self::InHandling => 'Dalam Penanganan',
            self::Resolved => 'Selesai Ditangani',
            self::Duplicate => 'Duplikat Laporan',
            self::Invalid => 'Laporan Tidak Valid',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Received => 'info',
            self::Verification, self::Dispatched => 'warning',
            self::ClarificationRequested => 'danger',
            self::InHandling => 'primary',
            self::Resolved => 'success',
            self::Duplicate => 'gray',
            self::Invalid => 'danger',
        };
    }
}
