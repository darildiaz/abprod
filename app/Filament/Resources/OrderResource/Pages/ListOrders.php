<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'Process' => Tab::make()->query(fn ($query) => $query->where('status', '0')),
            'Complet' => Tab::make()->query(fn ($query) => $query->where('status', '1')),
            'envio' => Tab::make()->query(fn ($query) => $query->where('status', '2')),
        ];
    }
}
