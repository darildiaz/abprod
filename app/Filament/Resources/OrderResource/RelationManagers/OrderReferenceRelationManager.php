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
                Forms\Components\TextInput::make('product_id')
                    ->required()
                    ->maxLength(255),
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
                    ->label('Product code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.code')
                    ->label('Product ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Productos')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('size.name')
                    ->label('Size ID')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('total_quantity')
                ->summarize(Sum::make())
                
                    ->label('Total Quantity'),
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
