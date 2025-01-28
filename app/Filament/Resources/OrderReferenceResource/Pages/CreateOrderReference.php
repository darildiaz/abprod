<?php

namespace App\Filament\Resources\OrderReferenceResource\Pages;

use App\Filament\Resources\OrderReferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderReference extends CreateRecord
{
    protected static string $resource = OrderReferenceResource::class;
}
