<?php

namespace App\Filament\Widgets;

use App\Models\ProductCategoryCounts;
use App\Models\ProductionError;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductionStatsWidget extends StatsOverviewWidget
{
    use HasWidgetShield;
    
    protected static ?string $pollingInterval = '10s';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;
    
    // Agregar propiedades para filtros
    public $selectedDate;
    public $selectedCenter;
    public $selectedCategory;

    public function mount(): void
    {
        $this->selectedDate = $this->getFilterValue('selectedDate', now()->toDateString());
        $this->selectedCenter = $this->getFilterValue('selectedCenter', null);
        $this->selectedCategory = $this->getFilterValue('selectedCategory', null);
    }

    protected function getFilterValue($property, $default)
    {
        // Si está disponible en la sesión (respaldo)
        if (session()->has("filament.{$property}")) {
            return session()->get("filament.{$property}");
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
        if (isset($filters['date'])) {
            $this->selectedDate = $filters['date'];
        }
        
        if (isset($filters['center'])) {
            $this->selectedCenter = $filters['center'];
        }
        
        if (isset($filters['category'])) {
            $this->selectedCategory = $filters['category'];
        }
        
        $this->refresh();
    }

    public function getStats(): array
    {
        $date = $this->selectedDate;
        $centerId = $this->selectedCenter;
        $categoryId = $this->selectedCategory;

        // Base query
        $productionQuery = ProductCategoryCounts::where('production_date', $date);

        // Apply filters
        if ($centerId) {
            $productionQuery->where('center_id', $centerId);
        }
        
        // Comentado porque la columna category_id no existe en la tabla
        /*if ($categoryId) {
            $productionQuery->where('category_id', $categoryId);
        }*/

        // Calculate metrics
        $totalProduction = $productionQuery->sum('total_quantity');
        
        // No contar por categoria porque la columna category_id no existe
        // $categoriesCount = $productionQuery->distinct('category_id')->count('category_id');
        $categoriesCount = 0; // Valor por defecto o eliminar esta variable
        
        // Error metrics
        $errorQuery = ProductionError::where('date', $date);
        
        if ($centerId) {
            $errorQuery->where('center_id', $centerId);
        }
        
        $totalErrors = $errorQuery->count();
        $errorRate = $totalProduction > 0 ? ($totalErrors / $totalProduction) * 100 : 0;

        return [
            Stat::make('Producción Total ' . Carbon::parse($date)->format('d/m/Y'), number_format($totalProduction))
                ->description($categoriesCount . ' categorías')
                ->descriptionIcon('heroicon-m-cube')
                ->chart([7, 4, 6, 8, 5, 9, 7])
                ->color('success'),
                
            Stat::make('Errores de Producción', number_format($totalErrors))
                ->description('Tasa de error: ' . round($errorRate, 2) . '%')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color($errorRate < 5 ? 'success' : ($errorRate < 10 ? 'warning' : 'danger')),
                
            Stat::make('Eficiencia', ($totalProduction > 0 && $totalErrors > 0) 
                ? (100 - round($errorRate, 2)) . '%'
                : '100%')
                ->description('Productos correctos vs total')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([100 - $errorRate])
                ->color($errorRate < 5 ? 'success' : ($errorRate < 10 ? 'warning' : 'danger')),
        ];
    }
} 