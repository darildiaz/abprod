<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;

use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\ProductCategoryCounts;
use Illuminate\Database\Eloquent\Builder; // ✅ Importar correctamente

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class ProductionCount extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
        ->query($this->getFilteredQuery()) // Aplica filtro de fecha por defecto
            ->columns([
            TextColumn::make('production_date')
                ->label('Fecha')
                ->searchable(),
            TextColumn::make('center_name')
                ->label('Centro')
                ->searchable(),
            TextColumn::make('category_name')
                ->label('Categoria')
                ->searchable(),

            TextColumn::make('total_quantity')
                ->label('Cantidad'),
            ])
            ->filters([
                Tables\Filters\Filter::make('production_date')
                    ->form([
                        Forms\Components\DatePicker::make('date')
                            ->label('Fecha')
                            ->default(Carbon::today()->toDateString()), // Valor por defecto "hoy"
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['date'], fn ($q, $date) => $q->where('production_date', $date));
                    })
                    ->indicateUsing(function (array $data) {
                        return !empty($data['date']) ? 'Fecha: ' . $data['date'] : null;
                    }),
                
            ]);
    }
    private function getFilteredQuery(): Builder
    {
        return ProductCategoryCounts::query()
            //->where('production_date', Carbon::today()->toDateString())
            ;
    }
}