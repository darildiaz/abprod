<?php

namespace App\Filament\Resources\ReorderResource\Pages;

use App\Filament\Resources\ReorderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReorder extends EditRecord
{
    protected static string $resource = ReorderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
