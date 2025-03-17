<?php

namespace App\Filament\Resources\RollResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RollProdtsRelationManager extends RelationManager
{
    protected static string $relationship = 'RollProdts';
    protected static ?string $title = 'Producciones';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('production_id')
                    ->relationship(
                        'production',
                        'order_id',
                        modifyQueryUsing: function (Builder $query) {
                            return $query->where('center_id', '=', 7);
                        }
                    )
                    
                    ->label('Orden')
                ->required()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('production')
            ->columns([
                Tables\Columns\TextColumn::make('production.date')
                    ->label('Fecha'),

                Tables\Columns\TextColumn::make('production.order_id')
                    ->label('Orden'),
                Tables\Columns\TextColumn::make('production.order.reference_name')
                    ->label('Referencia'),
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
