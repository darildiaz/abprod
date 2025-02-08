<?php

namespace App\Filament\Resources\ErrorOrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReorderRelationManager extends RelationManager
{
    protected static string $relationship = 'reorder';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                ->required(),
        
            Forms\Components\Select::make('center_id')
                ->label('Centro')
                ->relationship('center', 'name')
                ->live()

                ->required(),
            Forms\Components\Select::make('operator_id')
                ->label('Operador')
                ->options(function (callable $get) {
                    return \App\Models\Operator::query()
                        ->where('center_id', $get('center_id'))
                        ->pluck('name', 'id')
                        ->toArray();
                })
                ->reactive()
                ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            //->recordTitleAttribute('center')
            ->columns([
                Tables\Columns\TextColumn::make('date'),
                Tables\Columns\TextColumn::make('center.name'),
                Tables\Columns\TextColumn::make('operator.name'),
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
