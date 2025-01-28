<?php

namespace App\Filament\Resources\OrderQuestionAnswerResource\Pages;

use App\Filament\Resources\OrderQuestionAnswerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrderQuestionAnswer extends EditRecord
{
    protected static string $resource = OrderQuestionAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
