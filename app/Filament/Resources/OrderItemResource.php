<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderItemResource\Pages;
use App\Filament\Resources\OrderItemResource\RelationManagers;
use App\Models\OrderItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static ?string $navigationGroup = 'Orders details';
    protected static ?string $pluralLabel = 'Detalles de pedidos'; 

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_id')
                    ->required()
                    ->numeric(),
                
               //     Forms\Components\Repeater::make('items')

                   // ->schema([
                        Forms\Components\Select::make('model_id')
                        ->label('Mold')
                        
                       ->relationship(
                        name: 'model',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query) => $query->where('order_id', 
                        
                        $get('order_id')
                        
                        ))
                        ->required(),

                    Forms\Components\TextInput::make('name')
                        ->label('Nombre')
                        ->required(),

                    Forms\Components\TextInput::make('number')
                        ->label('Numero'),

                    Forms\Components\TextInput::make('other')
                        ->label('Otros datos'),

                    Forms\Components\Select::make('size_id')
                        ->label('Talla')
                        ->relationship('size', 'name') // Relación con la tabla sizes

                     //   ->options(Size::pluck('name', 'id'))
                        ->required(),

                    Forms\Components\TextInput::make('quantity')
                        ->label('Cantidad')
                        ->numeric()
                        ->required(),
                        Forms\Components\TextInput::make('subtotal')
                        ->label('Subtotal')
                        ->numeric()
                        ->required(),
                        Forms\Components\TagsInput::make('tags')->label('Products')
                        /*Forms\Components\Select::make('multi')
                        ->multiple()
                        ->label('Products')
                        ->relationship(
                            name: 'reference',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query) => $query->where('order_id', 
                            21
                            //$get('order_id')
                            
                            ))*/

                        ->placeholder('Add product names (comma-separated)'), // Productos relacionados al ítem
                
                    // Productos asociados al ítem
                  /*  Forms\Components\Repeater::make('products')
                        ->label('Products')
                        ->schema([
                               Forms\Components\Select::make('reference_id')
                               ->label('Reference')
                             //  ->relationship('reference', 'name'), // Relación con el modelo OrderReference
                        
                               ->relationship(
                                name: 'reference',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->where('order_id', 
                                21
                                //$get('order_id')
                                
                                ))
                                ->required(),
                            Forms\Components\TextInput::make('price')
                                ->label('Price')
                                ->numeric()
                                ->disabled(), // Calculado automáticamente

                            Forms\Components\TextInput::make('quantity')
                                ->label('Quantity')
                                ->numeric()
                                ->required(),
                        ])
                        ->minItems(1), // Al menos un producto por ítem
                        */
              //  ])
              //  ->required()
         //       ->minItems(1); // Al menos un ítem en la orden

                 /*   ])
                    ->visible(fn (callable $get) => $get('order_id') != null)
                    ->columns(7)
                    ->columnSpan('full'),    
                    
                    */
                

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order.reference_name')
                ->sortable(),

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
            'index' => Pages\ListOrderItems::route('/'),
            'create' => Pages\CreateOrderItem::route('/create'),
            'edit' => Pages\EditOrderItem::route('/{record}/edit'),
        ];
    }
}
