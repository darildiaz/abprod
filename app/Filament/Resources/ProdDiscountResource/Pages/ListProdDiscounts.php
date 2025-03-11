<?php

namespace App\Filament\Resources\ProdDiscountResource\Pages;

use App\Filament\Resources\ProdDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProdDiscounts extends ListRecords
{
    protected static string $resource = ProdDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
