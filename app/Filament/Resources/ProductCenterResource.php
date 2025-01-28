<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductCenterResource\Pages;
use App\Filament\Resources\ProductCenterResource\RelationManagers;
use App\Models\ProductCenter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductCenterResource extends Resource
{
    protected static ?string $model = ProductCenter::class;
    public static ?string $navigationIcon = 'heroicon-o-link';
    public static ?string $navigationGroup = 'Production';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\BelongsToSelect::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'code') // Relación con el modelo Product
                    ->required(),

                Forms\Components\BelongsToSelect::make('center_id')
                    ->label('Center')
                    ->relationship('center', 'name') // Relación con el modelo Center
                    ->required(),

                Forms\Components\TextInput::make('price')
                    ->label('Price')
                    ->numeric() // Validar que sea un número
                    ->required()
                    ->minValue(0) // Asegurarse de que sea positivo
                    ->suffix('Gs.'), // Mostrar "USD" como indicador de la moneda
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.code')
                    ->label('Product Code'), // Relación con Product
                Tables\Columns\TextColumn::make('center.name')
                    ->label('Center Name'), // Relación con Center
                Tables\Columns\TextColumn::make('price'),
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
            'index' => Pages\ListProductCenters::route('/'),
            'create' => Pages\CreateProductCenter::route('/create'),
            'edit' => Pages\EditProductCenter::route('/{record}/edit'),
        ];
    }
}
