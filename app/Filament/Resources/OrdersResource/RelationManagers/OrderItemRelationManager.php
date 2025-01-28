<?php

namespace App\Filament\Resources\OrdersResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemRelationManager extends RelationManager
{
    protected static string $relationship = 'orderItem';
    protected static ?string $recordTitleAttribute = 'order_id';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('number')
                    ->label('Number')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('other')
                    ->label('Other Details')
                    ->maxLength(255),

                Forms\Components\TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make('size_id')
                    ->label('Size')
                    ->relationship('size', 'name') // Relación con el modelo Size
                    ->required(),

                Forms\Components\Select::make('model_id')
                    ->label('Mold')
                    ->relationship('model', 'title') // Relación con el modelo Mold
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('number')
                    ->label('Number')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->sortable(),

                Tables\Columns\TextColumn::make('size.name')
                    ->label('Size'),

                Tables\Columns\TextColumn::make('model.title')
                    ->label('Mold'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
