<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Query\Expression;
use App\Models\OrderReference;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Columns\Summarizers\Sum;
class OrderReferenceRelationManager extends RelationManager
{
    protected static string $relationship = 'orderReferenceSummaries';
    protected static ?string $title = 'Resumen de Productos';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                
            ]);
    }
   

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('reference')
            ->defaultGroup('product.code')

            ->groups([
                Group::make('product.code')
                ->collapsible(), 
                Group::make('size.name')
                ->collapsible(),
            ])
            ->reorderable('size_id')

            ->columns([
                Tables\Columns\TextColumn::make('new_code')
                    ->label('Codigo externo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.code')
                    ->label('codigo producto')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Productos')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('size.name')
                    ->label('talle')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('total_quantity')
                ->summarize(Sum::make())
                
                    ->label('Cantidad total'),
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
