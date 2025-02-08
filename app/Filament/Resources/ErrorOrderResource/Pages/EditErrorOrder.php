<?php

namespace App\Filament\Resources\ErrorOrderResource\Pages;

use App\Filament\Resources\ErrorOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditErrorOrder extends EditRecord
{
    protected static string $resource = ErrorOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
