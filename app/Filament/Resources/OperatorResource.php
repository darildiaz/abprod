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
    protected static ?string $navigationGroup = "Production";
    public static ?string $navigationIcon = 'heroicon-o-identification';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                ->label('User')
                ->relationship('user', 'name') // Relación con el modelo Category
                ->required(),
                Forms\Components\Select::make('center_id')
                ->label('Center')
                ->relationship('center', 'name') // Relación con el modelo Category
                ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('position')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User Name')
                    ->sortable(), // Relación con User
                Tables\Columns\TextColumn::make('center.name')
                    ->label('Center Name'), // Relación con Center
                Tables\Columns\TextColumn::make('name')
                    ->label('Operator Name')->searchable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Position'),
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
