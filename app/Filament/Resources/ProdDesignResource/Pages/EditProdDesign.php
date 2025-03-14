<?php

namespace App\Filament\Resources\ProdDesignResource\Pages;

use App\Filament\Resources\ProdDesignResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProdDesign extends EditRecord
{
    protected static string $resource = ProdDesignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
