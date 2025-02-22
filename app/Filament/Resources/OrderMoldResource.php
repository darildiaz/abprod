<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderMoldResource\Pages;
use App\Filament\Resources\OrderMoldResource\RelationManagers;
use App\Models\OrderMold;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderMoldResource extends Resource
{
    protected static ?string $model = OrderMold::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static ?string $navigationGroup = 'Orders details';
    protected static ?string $pluralLabel = 'Modelos por pedido'; 

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('imagen')->label('Image')
                ->directory('orders')


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('imagen')
                ->label('Imagen')
                ->disk('public') // Especifica el disco de almacenamiento
                ->size(100), 
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
            'index' => Pages\ListOrderMolds::route('/'),
            'create' => Pages\CreateOrderMold::route('/create'),
            'edit' => Pages\EditOrderMold::route('/{record}/edit'),
        ];
    }
}
