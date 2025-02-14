<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassCenterResource\Pages;
use App\Filament\Resources\ClassCenterResource\RelationManagers;
use App\Models\ClassCenter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClassCenterResource extends Resource
{
    protected static ?string $model = ClassCenter::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = "Planificacion";
    protected static ?int $navigationSort = 6;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->label('Clasificacion')
                    ->relationship('classification', 'name')
                    ->default(1) // Predetermina el usuario logueado
                    ->live()
                    ->required(),
                Forms\Components\Select::make('center_id')
                    ->label('Centro')
                    ->relationship('center', 'name') // Relación con el modelo Center
                    ->required(),
                Forms\Components\TextInput::make('item')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('classification.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('center.name')
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
                Tables\Columns\TextColumn::make('item')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListClassCenters::route('/'),
            'create' => Pages\CreateClassCenter::route('/create'),
            'view' => Pages\ViewClassCenter::route('/{record}'),
            'edit' => Pages\EditClassCenter::route('/{record}/edit'),
        ];
    }
}
