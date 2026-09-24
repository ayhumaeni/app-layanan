<?php

declare(strict_types=1);

namespace App\Enums;

enum RehabilitationCaseStatus: string
{
    case Received = 'received';
    case Assessment = 'assessment';
    case ServicePlanning = 'service_planning';
    case InService = 'in_service';
    case Monitoring = 'monitoring';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Received => 'Kasus Diterima',
            self::Assessment => 'Tahap Assessment',
            self::ServicePlanning => 'Rencana Pelayanan',
            self::InService => 'Dalam Pelayanan',
            self::Monitoring => 'Monitoring Perkembangan',
            self::Closed => 'Kasus Selesai / Ditutup',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Received => 'info',
            self::Assessment, self::ServicePlanning => 'warning',
            self::InService, self::Monitoring => 'primary',
            self::Closed => 'success',
        };
    }
}
