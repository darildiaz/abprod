<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Product;
class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            
            Actions\ReplicateAction::make()
           ->record($this->record)
           ->beforeReplicaSaved(function (Product $replicatedProduct) {
            // Generar un código único basado en el original
            $replicatedProduct->code = self::generateUniqueCode($replicatedProduct->code);
            
            // Guardar el cambio
            $replicatedProduct->save();
        }),
        
        ];
    }
    protected static function splitString(string $input): array
{
    // Obtener la longitud de la cadena
    $length = strlen($input);

    // Extraer los últimos 2 caracteres (números)
    $lastTwoDigits = substr($input, -2);

    // Extraer el resto de la cadena (sin los últimos 2 caracteres)
    $baseString = substr($input, 0, $length - 2);

    return [$baseString, $lastTwoDigits];
}

/**
 * Generate a unique product code by incrementing the last two digits.
 */
protected static function generateUniqueCode(string $originalCode): string
{
    // Dividir el código en base y número
    [$baseCode, $currentNumber] = self::splitString($originalCode);

    // Convertir el número a entero
    $currentNumber = (int) $currentNumber;

    // Buscar todos los códigos en la base de datos que comiencen con la misma base
    $existingCodes = Product::where('code', 'LIKE', "{$baseCode}%")
        ->pluck('code')
        ->toArray();

    // Encontrar el número más alto entre los códigos existentes
    $maxNumber = $currentNumber;
    foreach ($existingCodes as $code) {
        [$base, $num] = self::splitString($code);
        $num = (int) $num;
        if ($num > $maxNumber) {
            $maxNumber = $num;
        }
    }

    // Generar el nuevo código incrementando el número más alto encontrado
    return "{$baseCode}" . str_pad($maxNumber + 1, 2, '0', STR_PAD_LEFT);
}
}
