<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
{
    // Procesar referencias_text
    if (!empty($data['references_text'])) {
        $references = explode("\n", $data['references_text']); // Dividir por filas (saltos de línea)

        foreach ($references as $reference) {
            [$name, $product_id, $price] = explode("\t", $reference);

            \App\Models\OrderReference::updateOrCreate(
                ['product_id' => trim($product_id)], // Clave única
                ['name' => trim($name), 'price' => trim($price)]
            );
        }
    }

    // Elimina el campo temporal para que no intente guardarse en la tabla orders
    unset($data['references_text']);

    return $data;
}
}
