<?php

namespace App\Filament\Resources\ProductCenterResource\Pages;

use App\Filament\Resources\ProductCenterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductCenter extends EditRecord
{
    protected static string $resource = ProductCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
