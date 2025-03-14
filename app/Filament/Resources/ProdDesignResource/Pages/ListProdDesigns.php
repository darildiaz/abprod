<?php

namespace App\Filament\Resources\ProdDesignResource\Pages;

use App\Filament\Resources\ProdDesignResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProdDesigns extends ListRecords
{
    protected static string $resource = ProdDesignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
