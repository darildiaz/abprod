<?php

namespace App\Filament\Resources\OrderQuestionAnswerResource\Pages;

use App\Filament\Resources\OrderQuestionAnswerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrderQuestionAnswers extends ListRecords
{
    protected static string $resource = OrderQuestionAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
