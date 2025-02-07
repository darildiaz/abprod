<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\Summarizers\Sum;
use Livewire\Component as LivewireComponent;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Actions\RedirectAction;
use Filament\Tables\Grouping\Group;
use App\Models\Category;
use App\Models\Order;
class CronogramaPage extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.cronograma-page';
    protected static ?string $navigationLabel = 'Reporte de Órdenes';
    protected static ?string $slug = 'reporte-ordenes';

    /**
     * Define la tabla de datos en la página.
     */
    public function table(Tables\Table $table): Tables\Table
{
    // Obtener solo las categorías importantes
    $categories = Category::where('is_important', 1)->pluck('name', 'id');

    $columns = [
        TextColumn::make('id')->label('ID Orden')->sortable()->searchable(),
        TextColumn::make('reference_name')->label('Referencia')->sortable(),
        textColumn::make('delivery_date')->label('Fecha de Entrega')->sortable(),
        TextColumn::make('status')
            ->badge()
            ->color(fn (string $state): string => match ($state) {
            '0' => 'gray',
            '1' => 'success',
            '2' => 'warning',
            default => 'secondary',
            })
            ->formatStateUsing(fn (string $state): string => match ($state) {
            '0' => 'pendiente',
            '1' => 'completado',
            '2' => 'enviado',
            default => $state,
            }),
        TextColumn::make('productions')
                    ->label('Center')
                    ->formatStateUsing(function ($state, $record) {
                        $maxCenter = collect($record->productions)
                            ->map(fn ($production) => $production->center)
                            ->sortByDesc('level')
                            ->first();
                            
                        return $maxCenter ? $maxCenter->name : '';
                    }),
    ];

    // Agregar dinámicamente columnas de categorías importantes
    foreach ($categories as $name) {
        $safeColumn = str_replace(' ', '_', strtolower($name));
        $columns[] = TextColumn::make($safeColumn)->label($name)->sortable()->summarize(Sum::make());
    }

    // Agregar la columna "otros"
    $columns[] = TextColumn::make('otros')->label('Otros')->sortable()->summarize(Sum::make());

    return $table
    ->defaultGroup('delivery_date')
    ->groups([ 
        Group::make('classification.name')
        ->label('Clasificacion')
        ->collapsible(),
        Group::make('delivery_date')
        ->label('Fecha de entrega')
        ->collapsible(),
    ])
->defaultSort('id', 'desc')
        ->query($this->getOrdersQuery())
        ->columns($columns)
        ->striped()
        
        ->filters([
            Tables\Filters\SelectFilter::make('status')
                  //  ->multiple()
                    ->options([
                0 => 'Pending',
                1 => 'Completed',
                2 => 'Enviado',
            ])
            ->default(0)
        ])
        ->bulkActions([
            Tables\Actions\BulkAction::make('showOrderReferenceSummaries')
            ->label('Ver Detalle de Referencias')
            ->action(fn (Collection $records) => $this->redirectToSummaryPage($records))
            ->requiresConfirmation(),
            
        ])
        ->paginated(50);
}



    /**
     * Genera la consulta SQL y devuelve un Builder de Eloquent.
     */
    protected function getOrdersQuery(): Builder
    {
        // Obtener solo las categorías importantes usando el modelo Category
        $categories = Category::where('is_important', 1)->pluck('name', 'id');

        // Construir la consulta usando el modelo Order (se asume que existe un modelo Order en App\Models\Order)
        $query =Order::query()
            ->select('orders.id', 'orders.reference_name', 'orders.delivery_date', 'orders.status')
            ->join('order_references as oref', 'orders.id', '=', 'oref.order_id')
            ->join('products as p', 'p.id', '=', 'oref.product_id')
            ->join('categories as c', 'c.id', '=', 'p.category_id');

        // Agregar dinámicamente columnas de categorías importantes
        foreach ($categories as $id => $name) {
            $safeColumn = str_replace(' ', '_', strtolower($name));
            $query->selectRaw("SUM(CASE WHEN c.id = ? THEN oref.quantity ELSE 0 END) as `$safeColumn`", [$id]);
        }

        // Agregar la columna "otros"
        $query->selectRaw("SUM(CASE WHEN c.is_important = 0 THEN oref.quantity ELSE 0 END) as otros");

        // Agrupar los resultados
        $query->groupBy('orders.id', 'orders.reference_name', 'orders.delivery_date', 'orders.status');

        return $query;
    }
public function redirectToSummaryPage(Collection $records)
{
    $orderIds = $records->pluck('id')->implode(','); // Convertimos los IDs en un string separado por comas

    return redirect()->to('/admin/order-reference-summaries?orderIds=' . $orderIds);
}


}

/**
 * Modelo temporal para la consulta dinámica.
 */
class OrderSummary extends Model
{
    protected $table = 'order_summaries';
    public $timestamps = false;
}
