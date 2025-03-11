<?php

namespace App\Filament\Resources\SizeGroupResource\Pages;

use App\Filament\Resources\SizeGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSizeGroup extends EditRecord
{
    protected static string $resource = SizeGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
