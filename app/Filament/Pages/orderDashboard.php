<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SalesGoalsBySellerChart;
use App\Filament\Widgets\SalesGoals;
use App\Filament\Widgets\SalesOverviewWidget;
use App\Filament\Widgets\TopSellersWidget;
use App\Filament\Widgets\OutstandingBalancesWidget;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Illuminate\Support\Carbon;

class orderDashboard extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Dashboard de Ventas';
    protected static ?string $navigationGroup = "Dashboard";
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.order-dashboard';

    public $selectedMonth;
    public $selectedYear;
    public $selectedTeam = null;
    public $selectedSeller = null;

    public function mount(): void
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SalesOverviewWidget::class,
            SalesGoalsBySellerChart::class,
            SalesGoals::class,
            TopSellersWidget::class,
            OutstandingBalancesWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('filter')
                ->label('Filtrar')
                ->form([
                    Select::make('month')
                        ->label('Mes')
                        ->options(array_combine(range(1, 12), [
                            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                        ]))
                        ->default($this->selectedMonth),
                    Select::make('year')
                        ->label('Año')
                        ->options(array_combine(range(now()->year - 2, now()->year), range(now()->year - 2, now()->year)))
                        ->default($this->selectedYear),
                    Select::make('team')
                        ->label('Equipo')
                        ->relationship('teams', 'name')
                        ->nullable()
                        ->placeholder('Todos los equipos'),
                    Select::make('seller')
                        ->label('Vendedor')
                        ->relationship('users', 'name')
                        ->nullable()
                        ->placeholder('Todos los vendedores'),
                ])
                ->action(function (array $data): void {
                    $this->selectedMonth = $data['month'];
                    $this->selectedYear = $data['year'];
                    $this->selectedTeam = $data['team'];
                    $this->selectedSeller = $data['seller'];
                    $this->refreshStatistics();
                }),
        ];
    }

    public function refreshStatistics(): void
    {
        // Emitir evento a widgets con los valores de filtrado
        $this->dispatch('filter-updated', [
            'month' => $this->selectedMonth,
            'year' => $this->selectedYear,
            'team' => $this->selectedTeam,
            'seller' => $this->selectedSeller,
        ]);
        
        // También almacenar en sesión como respaldo
        session()->put('filament.selectedMonth', $this->selectedMonth);
        session()->put('filament.selectedYear', $this->selectedYear);
        session()->put('filament.selectedTeam', $this->selectedTeam);
        session()->put('filament.selectedSeller', $this->selectedSeller);
        
        $this->dispatch('refresh-widgets');
    }
}
