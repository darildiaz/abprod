<?php

namespace App\Filament\Resources\ProdDiscountResource\Pages;

use App\Filament\Resources\ProdDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProdDiscount extends EditRecord
{
    protected static string $resource = ProdDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
