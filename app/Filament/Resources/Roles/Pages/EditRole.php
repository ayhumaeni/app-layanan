<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\PermissionRegistrar;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action, $record) {
                    $systemRoles = [
                        'administrator',
                        'petugas_dinsos',
                        'pejabat_penandatangan',
                        'pimpinan',
                        'operator_kecamatan_desa',
                        'masyarakat',
                    ];

                    if (in_array($record->name, $systemRoles, true)) {
                        Notification::make()
                            ->title('Gagal Menghapus')
                            ->body('Peran bawaan sistem ('.$record->name.') tidak boleh dihapus!')
                            ->danger()
                            ->send();

                        $action->halt();
                    }
                }),
        ];
    }

    protected function afterSave(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
