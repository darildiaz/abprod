<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialListResource\Pages;
use App\Filament\Resources\MaterialListResource\RelationManagers;
use App\Models\MaterialList;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaterialListResource extends Resource
{
    protected static ?string $model = MaterialList::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\select::make('product_id')
                    ->relationship( 'product', 'code')
                    ->label('Productos')    
                    ->required(),
                Forms\Components\select::make('material_id')
                    ->relationship( 'material', 'name')
                    ->label('Materiales')
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                Forms\Components\select::make('size_group_id')
                    ->relationship( 'sizeGroup', 'name')
                    ->label('Grupo de Tallas')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.code')
                    ->numeric()
                    ->label('Productos')
                    ->sortable(),
                Tables\Columns\TextColumn::make('material.name')
                    ->label('Materiales')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                ->label('Cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sizeGroup.name')
                    ->label('Grupo de Tallas')
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
            'index' => Pages\ListMaterialLists::route('/'),
            'create' => Pages\CreateMaterialList::route('/create'),
            'view' => Pages\ViewMaterialList::route('/{record}'),
            'edit' => Pages\EditMaterialList::route('/{record}/edit'),
        ];
    }
}
