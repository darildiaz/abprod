<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaterialListRelationManager extends RelationManager
{
    protected static string $relationship = 'MaterialLists';
    //protected $label='Lista de Materiales';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Forms\Components\select::make('material_id')
                    ->relationship( 'material', 'name')
                    ->label('Materiales')
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                Forms\Components\select::make('size_group_id')
                    ->relationship( 'sizeGroup', 'name')
                    ->label('Grupo de Tallas')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Lista de materiales')
            ->columns([
                
                Tables\Columns\TextColumn::make('material.name')
                    ->label('Materiales')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                ->label('Cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sizeGroup.name')
                    ->label('Grupo de Tallas')
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
