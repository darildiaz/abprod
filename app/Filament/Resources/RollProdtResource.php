<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RollProdtResource\Pages;
use App\Filament\Resources\RollProdtResource\RelationManagers;
use App\Models\RollProdt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RollProdtResource extends Resource
{
    protected static ?string $model = RollProdt::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('roll_id')
                    ->required()
                    ->relationship('roll', 'id'),
                Forms\Components\select::make('production_id')
                    ->relationship( 'production', 'id')
                    ->label('Producción'),
                Forms\Components\select::make('error_order_id')
                    ->relationship( 'errorOrder', 'order_id')
                    ->label('Orden de Error')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('roll_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('production_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('error_order_id')
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
            'index' => Pages\ListRollProdts::route('/'),
            'create' => Pages\CreateRollProdt::route('/create'),
            'view' => Pages\ViewRollProdt::route('/{record}'),
            'edit' => Pages\EditRollProdt::route('/{record}/edit'),
        ];
    }
}
