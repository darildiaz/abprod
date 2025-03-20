<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ProductionCount;
use App\Filament\Widgets\ProductionStatsWidget;
use App\Filament\Widgets\ProductionTrendsWidget;
use App\Filament\Widgets\ProductionErrorsWidget;
use App\Filament\Widgets\ProductionEfficiencyWidget;
use App\Models\ProductionCenter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Illuminate\Support\Facades\DB;

class ProductionDashboard extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'Dashboard de Producción';
    protected static ?string $navigationGroup = "Dashboard";
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.pages.production-dashboard';

    public $selectedDate;
    public $selectedCenter = null;
    public $selectedCategory = null;
    public $dateRange = [
        'start' => null,
        'end' => null,
    ];

    public function mount(): void
    {
        $this->selectedDate = now()->toDateString();
        $this->dateRange['start'] = now()->startOfMonth()->toDateString();
        $this->dateRange['end'] = now()->toDateString();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProductionStatsWidget::class,
            ProductionTrendsWidget::class,
            ProductionErrorsWidget::class,
            ProductionEfficiencyWidget::class,
            ProductionCount::class,
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
                    Forms\Components\Grid::make(2)
                        ->schema([
                            DatePicker::make('date')
                                ->label('Fecha')
                                ->default($this->selectedDate),
                            Select::make('center')
                                ->label('Centro de Producción')
                                ->options(ProductionCenter::pluck('name', 'id'))
                                ->nullable()
                                ->placeholder('Todos los centros'),
                        ]),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Select::make('category')
                                ->label('Categoría')
                                ->options(DB::table('product_categories')->pluck('name', 'id'))
                                ->nullable()
                                ->placeholder('Todas las categorías'),
                            DatePicker::make('dateRange')
                                ->label('Rango de fechas para tendencias')
                                ->default($this->selectedDate)
                                ->range()
                                ->default([
                                    'start' => $this->dateRange['start'],
                                    'end' => $this->dateRange['end'],
                                ]),
                        ]),
                ])
                ->action(function (array $data): void {
                    $this->selectedDate = $data['date'];
                    $this->selectedCenter = $data['center'];
                    $this->selectedCategory = $data['category'];
                    $this->dateRange = $data['dateRange'];
                    $this->refreshStatistics();
                }),
        ];
    }

    public function refreshStatistics(): void
    {
        // Emitir evento a widgets con los valores de filtrado
        $this->dispatch('filter-updated', [
            'date' => $this->selectedDate,
            'center' => $this->selectedCenter,
            'category' => $this->selectedCategory,
            'dateRange' => $this->dateRange,
        ]);
        
        // También almacenar en sesión como respaldo
        session()->put('filament.selectedDate', $this->selectedDate);
        session()->put('filament.selectedCenter', $this->selectedCenter);
        session()->put('filament.selectedCategory', $this->selectedCategory);
        session()->put('filament.dateRange', $this->dateRange);
        
        $this->dispatch('refresh-widgets');
    }
} 