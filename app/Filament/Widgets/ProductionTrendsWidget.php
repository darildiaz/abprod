<?php

namespace App\Filament\Widgets;

use App\Models\ProductCategoryCounts;
use App\Models\ProductionError;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ProductionTrendsWidget extends ChartWidget
{
    use HasWidgetShield;
    
    protected static ?string $heading = 'Tendencias de Producción';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;
    
    // Agregar propiedades para filtros
    public $selectedCenter;
    public $selectedCategory;
    public $dateRange = [
        'start' => null,
        'end' => null,
    ];

    public function mount(): void
    {
        $this->selectedCenter = $this->getFilterValue('selectedCenter', null);
        $this->selectedCategory = $this->getFilterValue('selectedCategory', null);
        $this->dateRange['start'] = $this->getFilterValue('dateRange.start', now()->startOfMonth()->toDateString());
        $this->dateRange['end'] = $this->getFilterValue('dateRange.end', now()->toDateString());
    }

    protected function getFilterValue($property, $default)
    {
        // Si está disponible en la sesión (respaldo)
        if (session()->has("filament.{$property}")) {
            return session()->get("filament.{$property}");
        }
        
        // Si es una propiedad anidada como dateRange.start
        if (str_contains($property, '.')) {
            list($parent, $child) = explode('.', $property);
            if (session()->has("filament.{$parent}") && isset(session()->get("filament.{$parent}")[$child])) {
                return session()->get("filament.{$parent}")[$child];
            }
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
        if (isset($filters['center'])) {
            $this->selectedCenter = $filters['center'];
        }
        
        if (isset($filters['category'])) {
            $this->selectedCategory = $filters['category'];
        }
        
        if (isset($filters['dateRange'])) {
            $this->dateRange = $filters['dateRange'];
        }
        
        $this->refresh();
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getData(): array
    {
        $startDate = Carbon::parse($this->dateRange['start']);
        $endDate = Carbon::parse($this->dateRange['end']);
        $centerId = $this->selectedCenter;
        $categoryId = $this->selectedCategory;
        
        $days = [];
        $productionData = [];
        $errorData = [];
        
        $currentDate = clone $startDate;
        while ($currentDate->lte($endDate)) {
            $days[] = $currentDate->format('d/m');
            
            // Get production for this day
            $productionQuery = ProductCategoryCounts::where('production_date', $currentDate->toDateString());
            
            if ($centerId) {
                $productionQuery->where('center_id', $centerId);
            }
            
            // Comentado porque la columna category_id no existe en la tabla
            /*if ($categoryId) {
                $productionQuery->where('category_id', $categoryId);
            }*/
            
            $production = $productionQuery->sum('total_quantity');
            $productionData[] = $production;
            
            // Get errors for this day
            $errorQuery = ProductionError::where('date', $currentDate->toDateString());
            
            if ($centerId) {
                $errorQuery->where('center_id', $centerId);
            }
            
            $errors = $errorQuery->count();
            $errorData[] = $errors;
            
            $currentDate->addDay();
        }
        
        return [
            'labels' => $days,
            'datasets' => [
                [
                    'label' => 'Producción',
                    'data' => $productionData,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'tension' => 0.1,
                ],
                [
                    'label' => 'Errores',
                    'data' => $errorData,
                    'borderColor' => 'rgb(255, 99, 132)',
                    'tension' => 0.1,
                ],
            ],
        ];
    }
} 