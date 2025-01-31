<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductionPackageResource\Pages;
use App\Filament\Resources\ProductionPackageResource\RelationManagers;
use App\Models\ProductionPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductionPackageResource extends Resource
{
    protected static ?string $model = ProductionPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_id')
                ->label('Order')
                ->live() // Esto hace que cuando se seleccione, se actualicen los productos
                ->afterStateUpdated(fn ($state, callable $set) => $set('productions', self::getOrderProducts($state))),

            Forms\Components\Repeater::make('productions')
                ->label('Productions')
                ->relationship('productions')
                ->schema([
                    Forms\Components\Select::make('product_id')
                        ->label('Product')
                        ->options(fn (callable $get) => self::getOrderProductOptions($get('order_id')))
                        ->required(),
                    Forms\Components\Select::make('size_id')
                        ->label('Size')
                        ->options(fn (callable $get) => self::getSizeOptions($get('product_id')))
                        ->required(),
                    Forms\Components\TextInput::make('quantity')
                        ->label('Quantity')
                        ->numeric()
                        ->required(),
                    Forms\Components\TextInput::make('price')
                        ->label('Price')
                        ->numeric()
                        ->required(),
                ])
                ->columns(2)
                ->hidden(fn (callable $get) => !$get('order_id'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
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
            'index' => Pages\ListProductionPackages::route('/'),
            'create' => Pages\CreateProductionPackage::route('/create'),
            'view' => Pages\ViewProductionPackage::route('/{record}'),
            'edit' => Pages\EditProductionPackage::route('/{record}/edit'),
        ];
    }
}
