<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderRelationManager extends RelationManager
{
    protected static string $relationship = 'Orders';

   

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('reference_name')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->label('ID'),
                Tables\Columns\TextColumn::make('reference_name')
                ->label('Nombre de la orden'),
                Tables\Columns\TextColumn::make('issue_date')
                ->label('Fecha de emisión'),
                Tables\Columns\TextColumn::make('delivery_date')
                ->label('Fecha de entrega prevista'),
                Tables\Columns\TextColumn::make('completion_date')
                ->label('Fecha de finalización'),
                Tables\Columns\TextColumn::make('total') ->money('Gs.'),
                Tables\Columns\TextColumn::make('status'),
                
            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
