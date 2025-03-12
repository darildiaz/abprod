<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RollResource\Pages;
use App\Filament\Resources\RollResource\RelationManagers;
use App\Models\Roll;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RollResource extends Resource
{
    protected static ?string $model = Roll::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                ->label('fecha')
                ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('impresora')
                ->label('impresora')    
                ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                ->label('Fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('impresora')
                    ->label('Impresora')
                    ->searchable(),
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
            RelationManagers\RollProdtsRelationManager::class,
            RelationManagers\RollErrorsRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRolls::route('/'),
            'create' => Pages\CreateRoll::route('/create'),
            'view' => Pages\ViewRoll::route('/{record}'),
            'edit' => Pages\EditRoll::route('/{record}/edit'),
        ];
    }
}
