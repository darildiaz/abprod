<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Tables\Columns\Summarizers\Sum;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class OrderItemRelationManager extends RelationManager
{
    protected static string $relationship = 'OrderItems';
    protected static ?string $title = 'Lista';
   
   
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('total')
            ->columns([
            Tables\Columns\TextColumn::make('item')
                ->label('Item')
                ->searchable(),   
            Tables\Columns\TextColumn::make('model')
                ->label('Modelo')
                ->numeric()
                ->sortable(),
            
            Tables\Columns\TextColumn::make('name')
                ->label('Nombre')
                ->searchable(),
            Tables\Columns\TextColumn::make('number')
                ->label('Numero')
                ->searchable(),
            Tables\Columns\TextColumn::make('other')
                ->label('Otro')
                ->searchable(),
            Tables\Columns\TextColumn::make('size.name')
                ->label('Talle')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('quantity')
                ->label('Cantidad')
                ->numeric()
                ->summarize(Sum::make())

                ->sortable(),
          
            Tables\Columns\TextColumn::make('references')
                ->label('References')
                ->getStateUsing(function ($record) {
                    return DB::table('order_references')
            ->join('products', 'order_references.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('order_references.order_id', $record->order_id)
            ->where('order_references.item', $record->item)
            ->join('sizes', 'order_references.size_id', '=', 'sizes.id') // Asumimos que hay una tabla sizes
            ->select('categories.name as catname', 'sizes.name as sizename') // Obtener la categoría y el tamaño
            ->get()
            ->map(function ($row) {
                // Devolver la categoría seguida del tamaño entre paréntesis
                return "{$row->catname}({$row->sizename})";
            })
            ->implode(' + '); // Unir todo con un signo de más
                
                }),
                Tables\Columns\TextColumn::make('references_c')
                ->label('References')
                ->getStateUsing(function ($record) {
                    return DB::table('order_references')
            ->join('products', 'order_references.product_id', '=', 'products.id')
            ->where('order_references.order_id', $record->order_id)
            ->where('order_references.item', $record->item)
            ->join('sizes', 'order_references.size_id', '=', 'sizes.id') // Asumimos que hay una tabla sizes
            ->select('products.code', 'sizes.name') // Obtener el código del producto y el tamaño
            ->get()
            ->map(function ($row) {
                // Devolver el código seguido del tamaño entre paréntesis
                return "{$row->code}({$row->name})";
            })
            ->implode(' + '); // Unir todo con un signo de más
                }),
                Tables\Columns\TextColumn::make('subtotal')
               // ->visible(false)
                
                ->visible(fn () => auth()->user()->can('seller_order'))
                ->summarize(Sum::make())
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
               // Tables\Actions\CreateAction::make(),
                
            ])
            ->actions([
               // Tables\Actions\EditAction::make(),
               // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make()
                ->exports([
                    ExcelExport::make()
                    //->withFilename(fn ($filename) => 'prefix-' . $filename)
                    ->withFilename(date('Y-m-d') . ' - Lista')
                    ,
                ]),   
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
    
}
