<?php

namespace App\Filament\Resources\ErrorOrderResource\Pages;

use App\Filament\Resources\ErrorOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewErrorOrder extends ViewRecord
{
    protected static string $resource = ErrorOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
