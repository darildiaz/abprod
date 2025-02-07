<?php

namespace App\Filament\Pages;

use App\Models\OrderReferenceSummary;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Grouping\Group;

class OrderReferenceSummaryPage extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Resumen de Referencias';
    protected static ?string $slug = 'order-reference-summaries';
    protected static string $view = 'filament.pages.order-reference-summary-page';

    #[Url] // Permite recibir parámetros en la URL
    public string $orderIds = '';

    /**
     * Define la tabla con los datos de `order_reference_summaries`.
     */
    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->defaultGroup('new_code')
            ->groups([ Group::make('new_code')
            ->label('Producto-talla')
            ->collapsible(),
            ])
            ->query($this->getQuery()) // Carga los datos de la base de datos
            ->columns([
                TextColumn::make('order_id')->label('Order ID')->sortable(),
                TextColumn::make('new_code')->label('Product Code')->sortable(),
                TextColumn::make('product.code')->label('Product Code')->sortable()->searchable(),
                TextColumn::make('size.name')->label('Size')->sortable(),
                TextColumn::make('total_quantity')->label('Total Quantity')->sortable()->summarize(Sum::make()),
            ])
            //->collapsed()
            ->paginated(50);
    }

    /**
     * Obtiene la consulta filtrada por los `order_id` seleccionados.
     */
    protected function getQuery(): Builder
    {
        $orderIdsArray = explode(',', $this->orderIds); // Convierte string en array

        return OrderReferenceSummary::query()
            ->whereIn('order_id', $orderIdsArray);
    }
}

