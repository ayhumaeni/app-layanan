<?php

declare(strict_types=1);

namespace App\Enums;

enum ServiceRequestHandler: string
{
    case Generic = 'generic';
    case Dtsen = 'dtsen';
    case Pbi = 'pbi';

    public function label(): string
    {
        return match ($this) {
            self::Generic => 'Umum',
            self::Dtsen => 'Surat Keterangan DTSEN',
            self::Pbi => 'Reaktivasi KIS / PBI-JK',
        };
    }
}
