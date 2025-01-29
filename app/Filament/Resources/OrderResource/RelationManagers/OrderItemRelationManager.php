<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemRelationManager extends RelationManager
{
    protected static string $relationship = 'OrderItem';

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
            ->recordTitleAttribute('reference_name')
            ->columns([
                

            Tables\Columns\TextColumn::make('model.title')
                ->numeric()
                ->sortable(),
            
            Tables\Columns\TextColumn::make('name')
                ->searchable(),
            Tables\Columns\TextColumn::make('number')
                ->searchable(),
            Tables\Columns\TextColumn::make('other')
                ->searchable(),
            Tables\Columns\TextColumn::make('size.name')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('quantity')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('tags')
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('copy_table')
                    ->label('Copy Table Data')
                    ->icon('heroicon-o-clipboard')
                    ->action(fn () => null) // No acción en PHP, solo en JS
                    ->extraAttributes([
                        'x-data' => '{}',
                        'x-on:click' => 'copyTableData()',
                    ]),
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
