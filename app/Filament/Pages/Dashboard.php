<?php

namespace App\Filament\Pages;

use App\Models\District;
use App\Models\ServiceType;
use App\Models\Village;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        $user = Auth::user();
        $isOperator = $user && $user->hasRole('operator_kecamatan_desa');

        return $schema
            ->components([
                Section::make('Filter Periode & Wilayah Dashboard')
                    ->description('Sesuaikan data dashboard berdasarkan rentang tanggal, jenis layanan, dan wilayah.')
                    ->collapsible()
                    ->components([
                        DatePicker::make('startDate')
                            ->label('Mulai Tanggal')
                            ->default(now()->subDays(30)->toDateString())
                            ->maxDate(now()),

                        DatePicker::make('endDate')
                            ->label('Sampai Tanggal')
                            ->default(now()->toDateString())
                            ->maxDate(now()),

                        Select::make('service_type_id')
                            ->label('Jenis Layanan')
                            ->placeholder('Semua Layanan')
                            ->options(ServiceType::query()->pluck('name', 'id'))
                            ->searchable(),

                        Select::make('district_id')
                            ->label('Wilayah Kecamatan')
                            ->placeholder('Semua Kecamatan')
                            ->options(District::query()->orderBy('name')->pluck('name', 'id'))
                            ->default($isOperator ? $user->district_id : null)
                            ->disabled($isOperator && ! empty($user->district_id))
                            ->searchable()
                            ->live(),

                        Select::make('village_id')
                            ->label('Wilayah Desa / Kelurahan')
                            ->placeholder('Semua Desa')
                            ->options(function ($get) use ($user, $isOperator) {
                                $districtId = $get('district_id') ?? ($isOperator ? $user->district_id : null);
                                if ($districtId) {
                                    return Village::where('district_id', $districtId)->orderBy('name')->pluck('name', 'id');
                                }

                                return Village::query()->orderBy('name')->pluck('name', 'id');
                            })
                            ->default($isOperator ? $user->village_id : null)
                            ->disabled($isOperator && ! empty($user->village_id))
                            ->searchable(),
                    ])
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                        'xl' => 5,
                    ]),
            ]);
    }
}
