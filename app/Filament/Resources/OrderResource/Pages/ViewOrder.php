<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('pdf') 
                    ->label('PDF')
                    ->color('success')
                    //->icon('heroicon-o-archive')
                    ->action(function (Model $record) {
                        return response()->streamDownload(function () use ($record) {
                            foreach ($record->orderItems as $item) {
                                $item->type = DB::table('order_references')
                                ->join('products', 'order_references.product_id', '=', 'products.id')
                                ->join('categories', 'products.category_id', '=', 'categories.id')
                                ->join('sizes', 'order_references.size_id', '=', 'sizes.id') // Asumimos que hay una tabla sizes
                                ->select(DB::raw('CONCAT(categories.name, " (", sizes.name, ")") as category_size'))
                                ->where('order_references.order_id', $record->id)
                                ->where('order_references.item', $item->item)
                                ->pluck('category_size') // Extraer el resultado concatenado
                                ->implode(' + '); // Unir todos los resultados con "+"
 
 
                                        
 
 
 
                                }
                            foreach ($record->orderMolds as $model) {
                                $qrCode = QrCode::size(100)->generate( asset('storage/' . $model->imagen ?? 'N/A' ));
                                // Convertir el QR a Base64 para insertarlo en el PDF
                                $qrCodeBase64 = base64_encode($qrCode);
                                
                                // Asignar el QR Base64 al modelo para pasarlo al Blade
                                $model->qr = 'data:image/svg+xml;base64,' . $qrCodeBase64;
                            }
                            echo Pdf::loadHtml(
                                Blade::render('pdf.invoice', ['order' => $record])
                            )->stream();
                        }, $record->id . ' Pedido.pdf');
                    }), 
        ];
    }
}
