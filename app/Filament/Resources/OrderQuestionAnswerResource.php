<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderQuestionAnswerResource\Pages;
use App\Filament\Resources\OrderQuestionAnswerResource\RelationManagers;
use App\Models\OrderQuestionAnswer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderQuestionAnswerResource extends Resource
{
    protected static ?string $model = OrderQuestionAnswer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Forms\Components\BelongsToSelect::make('order_id')
                    ->label('Order')
                    ->relationship('order', 'reference_name') // Relación con pedidos
                    ->searchable()
                    ->required(),

                Forms\Components\BelongsToSelect::make('question_id')
                    ->label('Question')
                    ->relationship('question', 'text') // Relación con preguntas
                    ->searchable()
                    ->required(),

                Forms\Components\Textarea::make('answer')
                    ->label('Answer')
                    ->required()
                    ->rows(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('question_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderQuestionAnswers::route('/'),
            'create' => Pages\CreateOrderQuestionAnswer::route('/create'),
            'edit' => Pages\EditOrderQuestionAnswer::route('/{record}/edit'),
        ];
    }
}
