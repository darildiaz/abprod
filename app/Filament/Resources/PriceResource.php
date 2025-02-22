<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceResource\Pages;
use App\Filament\Resources\PriceResource\RelationManagers;
use App\Models\Price;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PriceResource extends Resource
{
    protected static ?string $model = Price::class;
    public static ?string $navigationGroup = 'Productos';


    protected static ?string $navigationLabel = 'Precios';
    protected static ?string $pluralLabel = 'Precios';  
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Producto')
                    ->relationship('product', 'code') // Relación con el modelo Category
                    ->required(),
                
                    Forms\Components\Select::make('size_id')
                    ->label('Size')
                    ->relationship('size', 'name') // Relación con el modelo Category
                    ->required(),
                    
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->suffix('Gs.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.code')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('size.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                ->money('Gs.')

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
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPrices::route('/'),
            'create' => Pages\CreatePrice::route('/create'),
            'view' => Pages\ViewPrice::route('/{record}'),
            'edit' => Pages\EditPrice::route('/{record}/edit'),
        ];
    }
}
