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
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ProductCenterResource extends Resource
{
    protected static ?string $model = ProductCenter::class;
    public static ?string $navigationIcon = 'heroicon-o-link';
    public static ?string $navigationGroup = 'Produccion';
    public static ?string $navigationLabel = 'ProduccionCentros';
    public static ?string $pluralLabel = ' Precios de Produccion por Centros';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Producto')
                    ->relationship('product', 'code') // Relación con el modelo Product
                    ->required(),
                Forms\Components\Select::make('center_id')
                    ->label('Centro')
                    ->relationship('center', 'name') // Relación con el modelo Center
                    ->required(),
                Forms\Components\Select::make('type_of_valuation')
                    ->label('Tipo de Valuación')
                    ->options([
                        1 => 'Unitario',
                        2 => 'Modelo talle',
                        3 => 'Modelo',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->label('Precio')
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
                ->searchable()    
                ->label('Codigo de Producto'), // Relación con Product
                Tables\Columns\TextColumn::make('center.name')
                    ->label('Centro'), // Relación con Center
                Tables\Columns\TextColumn::make('price')
                ->label('Precio')
                ->money('Gs.')
                ,
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
                Tables\Filters\SelectFilter::make('center_id')
                    ->label('Centro')
                    ->relationship('center', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ExportBulkAction::make(),   

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
