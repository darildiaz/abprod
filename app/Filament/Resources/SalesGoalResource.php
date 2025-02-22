<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesGoalResource\Pages;
use App\Filament\Resources\SalesGoalResource\RelationManagers;
use App\Models\SalesGoal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesGoalResource extends Resource
{
    protected static ?string $model = SalesGoal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Metas de Ventas';

    protected static ?string $navigationGroup = "ventas";
    protected static ?int $navigationSort = 5;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('team_id')
                ->label('Equipo')
                    ->required()
                    ->relationship('team', 'name')
                    ,
                Forms\Components\TextInput::make('user_id')
                ->label('Vendedor')    
                ->required()
                    ->relationship('user', 'name')

                    ->numeric(),
                Forms\Components\TextInput::make('month')
                ->label('Mes')    
                ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('year')
                ->label('Año')    
                ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('amount')
                ->label('Monto')    
                ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team.name')
                ->label('Equipo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                ->label('Vendedor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('month')
                ->label('Mes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                ->label('Año')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                ->label('Monto')
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
            'index' => Pages\ListSalesGoals::route('/'),
            'create' => Pages\CreateSalesGoal::route('/create'),
            'view' => Pages\ViewSalesGoal::route('/{record}'),
            'edit' => Pages\EditSalesGoal::route('/{record}/edit'),
        ];
    }
}
