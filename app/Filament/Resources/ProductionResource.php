<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductionResource\Pages;
use App\Filament\Resources\ProductionResource\RelationManagers;
use App\Models\Production;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductionResource extends Resource
{
    protected static ?string $model = Production::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    public static ?string $navigationGroup = 'Production';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('center_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('operator_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('product_id')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),




                    Forms\Components\Select::make('order_id')
                    ->label('Order')
                    ->relationship('order', 'reference_name')
                  //  ->getOptionLabelFromRecordUsing(fn (Order $record) => "{$record->id} - {$record->reference_name}")
                    ->searchable(['id','reference_name']) // Hacer searchable por referencia
                    ->live() // Recargar el formulario al cambiar el pedido
                    ->afterStateUpdated(fn ($state, $set) => 
                    $set('products', self::getOrderProducts($state)))
                    
                    ->required(),

                    Forms\Components\Section::make('Production Summary')
                    ->hidden(fn ($get) => !$get('order_id')) // Se muestra solo si hay una orden
                    ->schema([
                        Forms\Components\Repeater::make('products')
                            ->label('Products from Order')
                            ->schema([
                                Forms\Components\TextInput::make('product_code')
                                    ->label('Product Code')
                                    ->disabled(),
    
                                    Forms\Components\TextInput::make('total_quantity')
                                    ->label('Total Quantity Ordered')
                                    ->numeric()
                                    ->disabled(),
                            ])
                            ->columns(2),
                    ]),
            
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('date')->label('Production Date')->date(),
                Tables\Columns\TextColumn::make('order.reference_name')->label('Order Reference'), // Relación con Order
                Tables\Columns\TextColumn::make('center.name')->label('Center Name'), // Relación con Center
                Tables\Columns\TextColumn::make('operator.name')->label('Operator Name'), // Relación con Operator
                Tables\Columns\TextColumn::make('product.code')->label('Product Code'), // Relación con Product
                Tables\Columns\TextColumn::make('quantity')->label('Quantity')->sortable(),
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
            'index' => Pages\ListProductions::route('/'),
            'create' => Pages\CreateProduction::route('/create'),
            'edit' => Pages\EditProduction::route('/{record}/edit'),
        ];
    }
    protected static function getOrderProducts($orderId)
    {
        if (!$orderId) return [];
    
        return DB::table('order_item_products as oip')
            ->join('order_items as oi', 'oip.order_item_id', '=', 'oi.id')
            ->join('order_references as orf', 'oip.reference_id', '=', 'orf.id')
            ->join('products as p', 'orf.product_id', '=', 'p.id')
            ->where('oi.order_id', $orderId)
            ->groupBy('p.id', 'p.code')
            ->selectRaw('p.id, p.code AS product_code, SUM(oip.quantity) AS total_quantity')
            ->get()
            ->map(fn ($item) => [
                'product_code' => $item->product_code,
                'total_quantity' => $item->total_quantity,
            ])
            ->toArray();
    }
}
