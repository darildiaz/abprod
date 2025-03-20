<?php

// namespace App\Filament\Widgets;

// use App\Models\ProductCategoryCounts;
// use App\Models\ProductionCenter;
// use App\Models\ProductionError;
// use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
// use Filament\Widgets\ChartWidget;

// class ProductionEfficiencyWidget extends ChartWidget
// {
//     use HasWidgetShield;
    
//     protected static ?string $heading = 'Eficiencia por Centro de Producción';
//     protected int | string | array $columnSpan = 'full';
//     protected static ?int $sort = 4;
    
//     // Agregar propiedades para filtros
//     public $selectedDate;

//     public function mount(): void
//     {
//         $this->selectedDate = $this->getFilterValue('selectedDate', now()->toDateString());
//     }

//     protected function getFilterValue($property, $default)
//     {
//         // Si está disponible en la sesión (respaldo)
//         if (session()->has("filament.{$property}")) {
//             return session()->get("filament.{$property}");
//         }
        
//         return $default;
//     }

//     // Listener para actualizar los filtros
//     protected function getListeners(): array
//     {
//         return [
//             'filter-updated' => 'updateFilters',
//         ];
//     }

//     public function updateFilters($filters): void
//     {
//         if (isset($filters['date'])) {
//             $this->selectedDate = $filters['date'];
//         }
        
//         $this->refresh();
//     }

//     protected function getType(): string
//     {
//         return 'bar';
//     }
    
//     protected function getData(): array
//     {
//         $date = $this->selectedDate;
        
//         $centers = ProductionCenter::all();
//         $centerNames = [];
//         $efficiencyData = [];
//         $backgroundColors = [];
        
//         foreach ($centers as $center) {
//             $centerNames[] = $center->name;
            
//             // Get production for this center
//             $production = ProductCategoryCounts::where('production_date', $date)
//                 ->where('center_id', $center->id)
//                 ->sum('total_quantity');
            
//             // Get errors for this center
//             $errors = ProductionError::where('date', $date)
//                 ->where('center_id', $center->id)
//                 ->count();
            
//             // Calculate efficiency
//             $efficiency = $production > 0 
//                 ? 100 - (($errors / $production) * 100) 
//                 : 100;
            
//             $efficiency = round($efficiency, 2);
            
//             // Assign color based on efficiency
//             $color = match (true) {
//                 $efficiency < 90 => 'rgba(255, 99, 132, 0.7)', // Red
//                 $efficiency < 95 => 'rgba(255, 205, 86, 0.7)', // Yellow
//                 default => 'rgba(75, 192, 192, 0.7)', // Green
//             };
            
//             $efficiencyData[] = $efficiency;
//             $backgroundColors[] = $color;
//         }
        
//         return [
//             'labels' => $centerNames,
//             'datasets' => [
//                 [
//                     'label' => 'Eficiencia (%)',
//                     'data' => $efficiencyData,
//                     'backgroundColor' => $backgroundColors,
//                 ],
//             ],
//         ];
//     }
    
//     protected function getOptions(): array
//     {
//         return [
//             'scales' => [
//                 'y' => [
//                     'min' => 80,
//                     'max' => 100,
//                 ],
//             ],
//         ];
//     }
// } 