<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\SalesGoal;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class SalesOverviewWidget extends StatsOverviewWidget
{
    use HasWidgetShield;
    
    protected static ?string $pollingInterval = '10s';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;
    
    // Agregar propiedades para filtros
    public $selectedMonth;
    public $selectedYear;
    public $selectedTeam;
    public $selectedSeller;

    public function mount(): void
    {
        $this->selectedMonth = $this->getFilterValue('selectedMonth', now()->month);
        $this->selectedYear = $this->getFilterValue('selectedYear', now()->year);
        $this->selectedTeam = $this->getFilterValue('selectedTeam', null);
        $this->selectedSeller = $this->getFilterValue('selectedSeller', null);
    }

    protected function getFilterValue($property, $default)
    {
        // Intenta obtener el valor de la página principal
        $livewire = null;
        
        if (method_exists($this, 'getMountedTableName')) {
            $livewire = $this->getMountedTableName();
        } elseif (property_exists($this, 'livewire') && !empty($this->livewire)) {
            $livewire = $this->livewire;
        } else {
            // Si no se puede obtener, intenta buscar en la sesión o eventos
            // Este es un enfoque de último recurso
            if (session()->has("filament.{$property}")) {
                return session()->get("filament.{$property}");
            }
        }
        
        if ($livewire && method_exists($livewire, $property)) {
            return $livewire->{$property};
        }
        
        return $default;
    }

    // Listener para actualizar los filtros
    protected function getListeners(): array
    {
        return [
            'filter-updated' => 'updateFilters',
        ];
    }

    public function updateFilters($filters): void
    {
        if (isset($filters['month'])) {
            $this->selectedMonth = $filters['month'];
        }
        
        if (isset($filters['year'])) {
            $this->selectedYear = $filters['year'];
        }
        
        if (isset($filters['team'])) {
            $this->selectedTeam = $filters['team'];
        }
        
        if (isset($filters['seller'])) {
            $this->selectedSeller = $filters['seller'];
        }
        
        $this->refresh();
    }

    public function getStats(): array
    {
        $month = $this->selectedMonth;
        $year = $this->selectedYear;
        $teamId = $this->selectedTeam;
        $sellerId = $this->selectedSeller;

        // Base query
        $orderQuery = Order::whereYear('issue_date', $year)
            ->whereMonth('issue_date', $month);

        // Apply filters
        if ($teamId) {
            $orderQuery->where('team_id', $teamId);
        }
        
        if ($sellerId) {
            $orderQuery->where('seller_id', $sellerId);
        }

        // Calculate metrics
        $totalSales = $orderQuery->sum('total');
        $orderCount = $orderQuery->count();
        
        // Average ticket
        $avgTicket = $orderCount > 0 ? $totalSales / $orderCount : 0;
        
        // Goal metrics
        $goalQuery = SalesGoal::where('year', $year)
            ->where('month', $month);

        if ($teamId) {
            $goalQuery->where('team_id', $teamId);
        }
        
        if ($sellerId) {
            $goalQuery->where('user_id', $sellerId);
        }
        
        $totalGoal = $goalQuery->sum('amount');
        $goalProgress = $totalGoal > 0 ? round(($totalSales / $totalGoal) * 100, 2) : 0;

        $periodLabel = Carbon::createFromDate($year, $month, 1)->format('F Y');

        return [
            Stat::make('Total Ventas ' . $periodLabel, '$' . number_format($totalSales, 2))
                ->description($orderCount . ' órdenes')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart([7, 4, 6, 8, 5, 9, 7])
                ->color($goalProgress >= 100 ? 'success' : ($goalProgress >= 75 ? 'warning' : 'danger')),
                
            Stat::make('Ticket Promedio', '$' . number_format($avgTicket, 2))
                ->description('por orden')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([5, 3, 7, 5, 4, 6, 5]),
                
            Stat::make('Meta de Ventas', '$' . number_format($totalGoal, 2))
                ->description($goalProgress . '% completado')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([$goalProgress])
                ->color($goalProgress >= 100 ? 'success' : ($goalProgress >= 75 ? 'warning' : 'danger')),
        ];
    }
} 