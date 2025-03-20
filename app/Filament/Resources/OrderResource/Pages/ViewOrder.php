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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

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
            Actions\Action::make('linkPublico') 
                    ->label('Enlace Público')
                    ->color('primary')
                    ->icon('heroicon-o-link')
                    ->action(function (Model $record) {
                        try {
                            // Generar URL firmada que expira en 7 días
                            $url = URL::temporarySignedRoute(
                                'orden.publica',
                                now()->addDays(7),
                                ['orderId' => $record->id]
                            );
                            
                            Log::info('URL pública generada para el pedido #' . $record->id . ': ' . $url);
                            
                            // Crear notificación usando el sistema de Filament
                            Notification::make()
                                ->title('Enlace público generado correctamente')
                                ->body('El enlace expirará en 7 días. Cópialo o compártelo ahora.')
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('copiar')
                                        ->label('Copiar enlace')
                                        ->button()
                                        ->color('primary')
                                        ->icon('heroicon-o-clipboard-document')
                                        ->extraAttributes([
                                            'onclick' => "navigator.clipboard.writeText('{$url}'); this.innerText = 'Copiado!'; setTimeout(() => { this.innerText = 'Copiar enlace' }, 2000)"
                                        ]),
                                    \Filament\Notifications\Actions\Action::make('abrir')
                                        ->label('Abrir enlace')
                                        ->button()
                                        ->url($url, true)
                                        ->icon('heroicon-o-arrow-top-right-on-square'),
                                ])
                                ->duration(10000)
                                ->persistent()
                                ->success()
                                ->send();
                            
                            return redirect()->back();
                        } catch (\Exception $e) {
                            Log::error('Error al generar enlace público: ' . $e->getMessage());
                            Notification::make()
                                ->title('Error al generar el enlace')
                                ->body('Se produjo un error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                            return redirect()->back();
                        }
                    }), 
        ];
    }
}
