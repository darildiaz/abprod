<?php

namespace App\Filament\Widgets;

use App\Models\ProductionError;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class ProductionErrorsWidget extends TableWidget
{
    use HasWidgetShield;
    
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3;
    
    // Agregar propiedades para filtros
    public $selectedDate;
    public $selectedCenter;

    public function mount(): void
    {
        $this->selectedDate = $this->getFilterValue('selectedDate', now()->toDateString());
        $this->selectedCenter = $this->getFilterValue('selectedCenter', null);
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
        
        $this->refresh();
    }

    public function getTableHeading(): string
    {
        return 'Errores de Producción';
    }

    public function table(Table $table): Table
    {
        $date = $this->selectedDate;
        $centerId = $this->selectedCenter;

        return $table
            ->query(
                ProductionError::where('date', $date)
                    ->when($centerId, fn($query) => $query->where('center_id', $centerId))
            )
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('center.name')
                    ->label('Centro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('part.name')
                    ->label('Tipo de Error')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50),
            ])
            ->filters([
            ])
            ->defaultSort('quantity', 'desc');
    }
} 