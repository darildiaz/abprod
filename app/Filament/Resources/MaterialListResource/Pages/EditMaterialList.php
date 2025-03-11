<?php

namespace App\Filament\Resources\MaterialListResource\Pages;

use App\Filament\Resources\MaterialListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaterialList extends EditRecord
{
    protected static string $resource = MaterialListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
