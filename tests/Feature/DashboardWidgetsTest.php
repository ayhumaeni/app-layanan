<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Widgets\DtsenPendingApprovalWidget;
use App\Filament\Widgets\DtsenPurposeChartWidget;
use App\Filament\Widgets\PbiEmergencyPriorityWidget;
use App\Filament\Widgets\PbiStageChartWidget;
use App\Filament\Widgets\RegionalDistributionChartWidget;
use App\Filament\Widgets\RehabilitationCasesChartWidget;
use App\Filament\Widgets\SapaSosialOverviewWidget;
use App\Filament\Widgets\ServiceAndComplaintTrendChartWidget;
use App\Models\User;
use App\Services\DashboardStatisticsService;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardWidgetsTest extends TestCase
{
    protected ?User $admin;

    protected ?User $petugas;

    protected ?User $pejabat;

    protected ?User $pimpinan;

    protected ?User $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('email', 'admin@blitarkab.go.id')->first();
        $this->petugas = User::where('email', 'petugas.dtsen@blitarkab.go.id')->first();
        $this->pejabat = User::where('email', 'kabid.linjamsos@blitarkab.go.id')->first();
        $this->pimpinan = User::where('email', 'sekdin@blitarkab.go.id')->first();
        $this->operator = User::where('email', 'operator.kanigoro@blitarkab.go.id')->first();
    }

    public function test_admin_can_access_dashboard_with_all_widgets(): void
    {
        $this->assertNotNull($this->admin);

        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Filter Periode & Wilayah Dashboard');
        $response->assertSee('Mulai Tanggal');
        $response->assertSee('Sampai Tanggal');
        $response->assertSee('Wilayah Kecamatan');

        // Test widget langsung untuk memastikan data stat dirender
        Livewire::actingAs($this->admin)
            ->test(SapaSosialOverviewWidget::class)
            ->assertSee('SK DTSEN Diterbitkan')
            ->assertSee('Menunggu Tanda Tangan')
            ->assertSee('PBI-JK Perlu Tindak Lanjut')
            ->assertSee('Kasus Rehabilitasi Aktif');

        Livewire::actingAs($this->admin)
            ->test(DtsenPendingApprovalWidget::class)
            ->assertSuccessful();

        Livewire::actingAs($this->admin)
            ->test(PbiEmergencyPriorityWidget::class)
            ->assertSuccessful();

        Livewire::actingAs($this->admin)
            ->test(DtsenPurposeChartWidget::class)
            ->assertSuccessful();

        Livewire::actingAs($this->admin)
            ->test(PbiStageChartWidget::class)
            ->assertSuccessful();

        Livewire::actingAs($this->admin)
            ->test(RehabilitationCasesChartWidget::class)
            ->assertSuccessful();

        Livewire::actingAs($this->admin)
            ->test(ServiceAndComplaintTrendChartWidget::class)
            ->assertSuccessful();

        Livewire::actingAs($this->admin)
            ->test(RegionalDistributionChartWidget::class)
            ->assertSuccessful();
    }

    public function test_pejabat_can_see_pending_approval_widget(): void
    {
        $this->assertNotNull($this->pejabat);

        $this->actingAs($this->pejabat);
        $this->assertTrue(DtsenPendingApprovalWidget::canView());
    }

    public function test_petugas_can_see_emergency_priority_widget(): void
    {
        $this->assertNotNull($this->petugas);

        $this->actingAs($this->petugas);
        $this->assertTrue(PbiEmergencyPriorityWidget::canView());
        $this->assertTrue(DtsenPurposeChartWidget::canView());
        $this->assertTrue(PbiStageChartWidget::canView());
        $this->assertTrue(RehabilitationCasesChartWidget::canView());
    }

    public function test_operator_dashboard_visibility_rules(): void
    {
        $this->assertNotNull($this->operator);

        $this->actingAs($this->operator);

        // Operator tidak melihat widget antrean tanda tangan pejabat
        $this->assertFalse(DtsenPendingApprovalWidget::canView());
        // Operator tetap melihat KPI overview, tren, dan sebaran
        $this->assertTrue(SapaSosialOverviewWidget::canView());
        $this->assertTrue(ServiceAndComplaintTrendChartWidget::canView());
        $this->assertTrue(RegionalDistributionChartWidget::canView());
    }

    public function test_statistics_service_calculates_correct_data_structure(): void
    {
        $service = app(DashboardStatisticsService::class);

        $overview = $service->getOverviewStats([], $this->admin);
        $this->assertArrayHasKey('dtsen_issued', $overview);
        $this->assertArrayHasKey('dtsen_pending', $overview);
        $this->assertArrayHasKey('pbi_follow_up', $overview);
        $this->assertArrayHasKey('rehab_active', $overview);
        $this->assertArrayHasKey('active_in_process', $overview);

        $purposeStats = $service->getDtsenByPurpose([], $this->admin);
        $this->assertArrayHasKey('labels', $purposeStats);
        $this->assertArrayHasKey('counts', $purposeStats);

        $pbiStages = $service->getPbiStages([], $this->admin);
        $this->assertArrayHasKey('labels', $pbiStages);
        $this->assertArrayHasKey('counts', $pbiStages);

        $rehabStats = $service->getRehabilitationStatusCounts([], $this->admin);
        $this->assertArrayHasKey('labels', $rehabStats);
        $this->assertArrayHasKey('counts', $rehabStats);

        $trend = $service->getTrendData([], $this->admin, 7);
        $this->assertArrayHasKey('labels', $trend);
        $this->assertArrayHasKey('requests', $trend);
        $this->assertArrayHasKey('complaints', $trend);
        $this->assertCount(7, $trend['labels']);

        $regional = $service->getRegionalDistribution([], $this->admin);
        $this->assertArrayHasKey('labels', $regional);
        $this->assertArrayHasKey('counts', $regional);
        $this->assertGreaterThan(0, count($regional['labels']));
    }

    public function test_statistics_service_scopes_data_for_operator(): void
    {
        $service = app(DashboardStatisticsService::class);
        $this->assertNotNull($this->operator);

        $regional = $service->getRegionalDistribution([], $this->operator);
        // Operator Kanigoro hanya mendapat 1 kecamatan (Kanigoro)
        $this->assertCount(1, $regional['labels']);
        $this->assertStringContainsString('Kanigoro', $regional['labels'][0]);
    }

    public function test_statistics_service_applies_date_filters(): void
    {
        $service = app(DashboardStatisticsService::class);

        $filters = [
            'startDate' => now()->subDays(5)->toDateString(),
            'endDate' => now()->toDateString(),
        ];

        $overview = $service->getOverviewStats($filters, $this->admin);
        $this->assertIsArray($overview);
    }
}
