<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderItemProductResource\Pages;
use App\Filament\Resources\OrderItemProductResource\RelationManagers;
use App\Models\OrderItemProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemProductResource extends Resource
{
    protected static ?string $model = OrderItemProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\BelongsToSelect::make('order_item_id')
                ->label('Order Item')
                ->relationship('orderItem', 'name') // Relación con el modelo OrderItem
                ->required()
                ->searchable(),

            // Selector de Order Reference
            Forms\Components\BelongsToSelect::make('reference_id')
                ->label('Order Reference')
                ->relationship('reference', 'name') // Relación con el modelo OrderReference
                ->required()
                ->searchable(),

            // Campo para la cantidad
            Forms\Components\TextInput::make('quantity')
                ->label('Quantity')
                ->required()
                ->numeric()
                ->minValue(1) // Cantidad mínima
                ->placeholder('Enter the quantity'),

                
                Forms\Components\TextInput::make('size_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_item_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('size_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
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
            'index' => Pages\ListOrderItemProducts::route('/'),
            'create' => Pages\CreateOrderItemProduct::route('/create'),
            'edit' => Pages\EditOrderItemProduct::route('/{record}/edit'),
        ];
    }
}
