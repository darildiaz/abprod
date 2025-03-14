<?php

namespace App\Filament\Resources\ProdDesignResource\Pages;

use App\Filament\Resources\ProdDesignResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProdDesign extends ViewRecord
{
    protected static string $resource = ProdDesignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
