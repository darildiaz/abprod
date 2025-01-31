<?php

namespace App\Filament\Resources\ProductionPackageResource\Pages;

use App\Filament\Resources\ProductionPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductionPackage extends EditRecord
{
    protected static string $resource = ProductionPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
