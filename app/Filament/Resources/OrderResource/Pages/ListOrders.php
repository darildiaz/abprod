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
            //Actions\
        ];
    }
    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'Espera' => Tab::make()->query(fn ($query) => $query->where('status', '0')),
            'Planificado' => Tab::make()->query(fn ($query) => $query->where('status', '1')),
            'Completado' => Tab::make()->query(fn ($query) => $query->where('status', '2')),
            'envio' => Tab::make()->query(fn ($query) => $query->where('status', '2')),
            'Cancelado' => Tab::make()->query(fn ($query) => $query->where('status', '=', '4')),
            'ventas' => Tab::make()->query(fn ($query) => $query->where('status', '!=', '4')),
        ];
    }
}
