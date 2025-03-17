<?php

namespace App\Filament\Resources\RollResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RollErrorsRelationManager extends RelationManager
{
    protected static string $relationship = 'RollErrors';
    protected static ?string $title = 'Reimpresion';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('error_order_id')
                    ->label('Error')
                    ->relationship('errorOrder', 'id')
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "({$record->id}) OT: {$record->order_id} - {$record->obs_det}")
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('error')
            ->columns([
                Tables\Columns\TextColumn::make('errorOrder.id')
                    ->label('id Error'),
                    Tables\Columns\TextColumn::make('errorOrder.order_id')
                    ->label('OT'),
                Tables\Columns\TextColumn::make('errorOrder.part.name'),
                Tables\Columns\TextColumn::make('errorOrder.obs_det'),
                Tables\Columns\TextColumn::make('errorOrder.quantity'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
