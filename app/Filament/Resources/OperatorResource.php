<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OperatorResource\Pages;
use App\Filament\Resources\OperatorResource\RelationManagers;
use App\Models\Operator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OperatorResource extends Resource
{
    protected static ?string $model = Operator::class;
    protected static ?string $navigationGroup = "Produccion";
    public static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Operadores';
    protected static ?string $pluralLabel = 'Operadores'; 

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                ->label('Usuario')
                ->relationship('user', 'name') // Relación con el modelo Category
                ->required(),
                Forms\Components\Select::make('center_id')
                ->label('Centro')
                ->relationship('center', 'name') // Relación con el modelo Category
                ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('position')
                    ->label('Posición')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nombre de Usuario')
                    ->sortable(), // Relación con User
                Tables\Columns\TextColumn::make('center.name')
                    ->label('Nombre de Centro'), // Relación con Center
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre de Operador')->searchable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Cargo'),
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
            'index' => Pages\ListOperators::route('/'),
            'create' => Pages\CreateOperator::route('/create'),
            'edit' => Pages\EditOperator::route('/{record}/edit'),
        ];
    }
}
