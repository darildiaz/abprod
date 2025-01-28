<?php

namespace App\Filament\Resources\OrderItemProductResource\Pages;

use App\Filament\Resources\OrderItemProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderItemProduct extends CreateRecord
{
    protected static string $resource = OrderItemProductResource::class;
}
