<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductiondetResource\Pages;
use App\Filament\Resources\ProductiondetResource\RelationManagers;
use App\Models\Productiondet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductiondetResource extends Resource
{
    protected static ?string $model = Productiondet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static ?string $navigationGroup = 'Produccion';
    public static ?string $navigationLabel = 'Produccion Detalles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\DatePicker::make('date')
                ->label('Fecha'),
            Forms\Components\TextInput::make('production_id')
                ->label('ID de Producción')
                ->required()
                ->numeric(),
            Forms\Components\TextInput::make('product_id')
                ->label('ID de Producto')
                ->required()
                ->numeric(),
            Forms\Components\TextInput::make('quantity')
                ->label('Cantidad')
                ->required()
                ->numeric(),
            Forms\Components\TextInput::make('price')
                ->label('Precio')
                ->required()
                ->numeric()
                ->prefix('$'),
            Forms\Components\Toggle::make('pay')
                ->label('Pago')
                ->required(),
            ]);
        }

        public static function table(Table $table): Table
        {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('production.date')
                ->label('Fecha de Producción')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('production.center.name')
                ->label('Centro de Producción')
                ->numeric()
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('product.code')
                ->label('Código del Producto')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('quantity')
                ->label('Cantidad')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('price')
                ->label('Precio')
                ->money()
                ->sortable(),
            Tables\Columns\IconColumn::make('pay')
                ->label('Pago Realizado')
                ->boolean(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Creado el')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->label('Actualizado el')
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
            'index' => Pages\ListProductiondets::route('/'),
            'create' => Pages\CreateProductiondet::route('/create'),
            'view' => Pages\ViewProductiondet::route('/{record}'),
            'edit' => Pages\EditProductiondet::route('/{record}/edit'),
        ];
    }
}
