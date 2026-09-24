<?php

declare(strict_types=1);

namespace App\Enums;

enum DocumentVerificationStatus: string
{
    case Pending = 'pending';
    case Valid = 'valid';
    case RevisionNeeded = 'revision_needed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Verifikasi',
            self::Valid => 'Sesuai / Valid',
            self::RevisionNeeded => 'Perlu Perbaikan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Valid => 'success',
            self::RevisionNeeded => 'danger',
        };
    }
}
