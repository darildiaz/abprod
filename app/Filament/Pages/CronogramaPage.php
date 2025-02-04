<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\Summarizers\Sum;

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
    $categories = DB::table('categories')
        ->where('is_important', 1)
        ->pluck('name', 'id');

    $columns = [
        TextColumn::make('id')->label('ID Orden')->sortable()->searchable(),
        TextColumn::make('reference_name')->label('Referencia')->sortable(),
        textColumn::make('delivery_date')->label('Fecha de Entrega')->sortable(),
        textColumn::make('status')->label('Estado')->sortable(),
    ];

    // Agregar dinámicamente columnas de categorías importantes
    foreach ($categories as $name) {
        $safeColumn = str_replace(' ', '_', strtolower($name));
        $columns[] = TextColumn::make($safeColumn)->label($name)->sortable()->summarize(Sum::make());
    }

    // Agregar la columna "otros"
    $columns[] = TextColumn::make('otros')->label('Otros')->sortable();

    return $table
        ->defaultGroup('delivery_date')
        ->query($this->getOrdersQuery())
        ->columns($columns)
        ->striped()
        ->filters([
            Tables\Filters\SelectFilter::make('status')
            ->multiple()
            ->options([
        0 => 'Pending',
        1 => 'Completed',
        2 => 'Enviado',
    ])
        ])
        
        ->paginated(50);
}



    /**
     * Genera la consulta SQL y devuelve un Builder de Eloquent.
     */
    protected function getOrdersQuery(): Builder
{
    // Obtener solo las categorías importantes
    $categories = DB::table('categories')
        ->where('is_important', 1)
        ->pluck('name', 'id');

    // Construir las columnas dinámicas solo para las categorías importantes
    $categoryColumns = '';
    foreach ($categories as $id => $name) {
        $safeColumn = str_replace(' ', '_', strtolower($name)); // Evita espacios en nombres de columnas
        $categoryColumns .= ", SUM(CASE WHEN c.id = $id THEN oref.quantity ELSE 0 END) AS `$safeColumn`";
    }

    // Construir la consulta completa
    $sql = "
        SELECT 
            o.id AS id,
            o.reference_name,
            o.delivery_date,
            o.status
            $categoryColumns, 
            SUM(CASE WHEN c.is_important = 0 THEN oref.quantity ELSE 0 END) AS otros
        FROM orders o
        JOIN order_references oref ON o.id = oref.order_id
        JOIN products p ON p.id = oref.product_id
        JOIN categories c ON p.category_id = c.id
        GROUP BY o.id, o.reference_name
    ";

    return OrderSummary::query()->fromSub(DB::table(DB::raw("($sql) as order_summaries")), 'order_summaries');
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
