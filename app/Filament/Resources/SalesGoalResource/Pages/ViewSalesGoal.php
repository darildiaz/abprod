<?php

namespace App\Filament\Resources\SalesGoalResource\Pages;

use App\Filament\Resources\SalesGoalResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSalesGoal extends ViewRecord
{
    protected static string $resource = SalesGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
