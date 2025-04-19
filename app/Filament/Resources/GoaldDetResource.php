<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GoaldDetResource\Pages;
use App\Filament\Resources\GoaldDetResource\RelationManagers;
use App\Models\GoaldDet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GoaldDetResource extends Resource
{
    protected static ?string $model = GoaldDet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Metas de Ventas/Productos';

    protected static ?string $navigationGroup = "ventas";
    protected static ?int $navigationSort = 5;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('sales_goal_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('salesgoal.team.name')
                    ->label('Equipo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('salesgoal.user.name')
                    ->label('Vendedor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('salesgoal.month')
                    ->label('Mes')
                    ->sortable(),
                Tables\Columns\TextColumn::make('salesgoal.year')
                    ->label('Año')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->money('Gs.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Total')
                    ->money('Gs.')
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
                Tables\Filters\SelectFilter::make('salesgoal.team_id')
                    ->label('Equipo')
                    ->relationship('salesgoal.team', 'name'),
                Tables\Filters\SelectFilter::make('salesgoal.user_id')
                    ->label('Vendedor')
                    ->relationship('salesgoal.user', 'name'),
                Tables\Filters\SelectFilter::make('salesgoal.month')
                    ->label('Mes')
                    ->options([1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre']),
                Tables\Filters\SelectFilter::make('salesgoal.year')
                    ->label('Año')
                    ->options(range(2020, now()->year)),
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
            'index' => Pages\ListGoaldDets::route('/'),
            'create' => Pages\CreateGoaldDet::route('/create'),
            'view' => Pages\ViewGoaldDet::route('/{record}'),
            'edit' => Pages\EditGoaldDet::route('/{record}/edit'),
        ];
    }
}
