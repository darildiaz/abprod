<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReorderResource\Pages;
use App\Filament\Resources\ReorderResource\RelationManagers;
use App\Models\Reorder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReorderResource extends Resource
{
    protected static ?string $model = Reorder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('error_order_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('center_id')
                    ->label('Centro')
                    ->relationship('center', 'name')
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getOrderProducts($set, $get))

                    ->required(),
                Forms\Components\Select::make('operator_id')
                    ->label('Operador')
                    ->options(function (callable $get) {
                        return \App\Models\Operator::query()
                            ->where('center_id', $get('center_id'))
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->reactive()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('error_order_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('center_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('operator_id')
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
            'index' => Pages\ListReorders::route('/'),
            'create' => Pages\CreateReorder::route('/create'),
            'view' => Pages\ViewReorder::route('/{record}'),
            'edit' => Pages\EditReorder::route('/{record}/edit'),
        ];
    }
}
