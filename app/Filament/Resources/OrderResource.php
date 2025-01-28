<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\RelationManagers\OrderItemRelationManager;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    public static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    public static ?string $navigationGroup = 'Orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('General')
                        ->schema([
                            

                    // Tab 1: Datos generales del cliente y pedido
                                    Forms\Components\Select::make('customer_id')
                                        ->label('Customer')
                                        ->relationship('customer', 'name')
                                        
                                        ->required(),

                                        Forms\Components\Select::make('seller_id')
                                        ->label('Seller')
                                        ->relationship('seller', 'name')
                                         ->default(auth()->id()) // Predetermina el usuario logueado

                                        ->required(),

                                        Forms\Components\TextInput::make('reference_name')
                                        ->label('Reference Name')
                                        ->required(),

                                        Forms\Components\TextInput::make('issue_date')
                                        ->label('Issue Date')
                                        ->default(today()->toDateString())
                                        ->type('date')
                                        ->required(),

                                        Forms\Components\TextInput::make('delivery_date')
                                        ->label('Delivery Date')
                                        
                                        ->type('date')
                                        ->required(),
                                        Forms\Components\Select::make('classification_id')
                                        ->label('clasificacion')
                                        ->relationship('classification', 'name')
                                         ->default(1) // Predetermina el usuario logueado

                                        ->required(),
                                        //Forms\Components\TextInput::make('total')->label('Total')->numeric()->required(),
                    ]) ->columns(3),
                    Forms\Components\Wizard\Step::make('Models')
                    ->schema([
                    // Tab 2: Cargar lista de modelos
                            Forms\Components\Repeater::make('models')->label('Models')->relationship('orderMolds')->schema([
                                Forms\Components\TextInput::make('title')->label('Model Title')->required(),
                                Forms\Components\FileUpload::make('imagen')->label('Image')->required(),
                            ])->columns(2)->required(),
                        ]),
                    Forms\Components\Wizard\Step::make('References')
                        ->schema([
                    // Tab 3: Cargar lista de referencias
  /*                          Forms\Components\TextArea::make('references_text')
                                ->label('References (Paste Text)')
                                ->placeholder("Producto Referencia\tCódigo de Producto\tPrecio\nCamiseta\tcam-s01\t100000\nShort\tsh-s01\t50000")
                                ->rows(5)
                                ->dehydrated(false) // Indica que no debe ser guardado en la base de datos
                                ->helperText('Paste references separated by TAB for columns and ENTER for rows.')
                                ->required(),
                            
*/

                                Forms\Components\Repeater::make('references')
                                ->label('References')
                                ->relationship('orderReferences') // Relación con la tabla order_references
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Product Name')
                                        ->required(),
                                    Forms\Components\Select::make('product_id')
                                        ->label('Product')
                                        ->relationship('product', 'code') // Relación con la tabla products
                                        ->required(),
                                    Forms\Components\TextInput::make('price')
                                        ->label('Price')
                                        ->numeric()
                                        ->required(),
                                ])
                                ->columns(3)
                                //->required()
                                ,
                            ]),
                            Forms\Components\Wizard\Step::make('Items')
                                ->schema([
                                    // Tab 4: Cargar lista de ítems de la orden
                                    Forms\Components\TextArea::make('order_items_text')
                                            ->label('Order Items (Paste Text)')
                                            ->placeholder("Item\tModelo\tNombre\tNúmero\tOtros\tTalle\tCantidad\tProductos\tSubtotal\n1\tOficial\tJugador 1\t10\t0rh+\tm-cab\t1\tCamiseta,Short\t150000\n2\tArquero\tJugador 2\t1\t0rh+\tg-cab\t1\tCamiseta,Short\t150000")
                                            ->rows(8)
                                            ->dehydrated(false) // Indica que no debe ser guardado en la base de datos
                                            ->helperText('Paste order items separated by TAB for columns and ENTER for rows.')
                                            ->required(),
                                Forms\Components\Repeater::make('orderItems')
                                    ->label('Order Items')
                                    ->relationship('orderItems') // Relación con la tabla order_items
                                    ->schema([
                                        Forms\Components\Select::make('model_id')
                                            ->label('Model')
                                            ->relationship('model', 'title') // Relación con los modelos (order_molds)
                                            
                                            ->required(),
                                        Forms\Components\TextInput::make('name')
                                            ->label('Name')
                                            ->required(),
                                        Forms\Components\TextInput::make('number')
                                            ->label('Number'),
                                        Forms\Components\TextInput::make('other')
                                            ->label('Other'),
                                        Forms\Components\Select::make('size_id')
                                            ->label('Size')
                                            ->relationship('size', 'name') // Relación con la tabla sizes
                                            ->required(),
                                        Forms\Components\TextInput::make('quantity')
                                            ->label('Quantity')
                                            ->numeric()
                                            ->required(),
                                        Forms\Components\TextInput::make('subtotal')
                                            ->label('Subtotal')
                                            ->numeric()
                                            ->required(),
                                        /*Forms\Components\MultiSelect::make('products')
                                            ->relationship('products', 'code') // Relación con `products` y muestra el campo `name`
                                            ->label('Products')
                                            ->placeholder('Select products for this order item')
                                            ->required(),*/
                                        Forms\Components\TagsInput::make('tags')
                                            ->label('Products')
                               // ->dehydrated(false) // Indica que no debe ser guardado en la base de datos

                                        ->placeholder('Add product names (comma-separated)'), // Productos relacionados al ítem
                                    ])
                                    ->columns(8)
                                    ->required(),

                                ]),
                                Forms\Components\Wizard\Step::make('Questions')
                            ->schema([

                            ]),
                            ])->columnSpan('full')


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('issue_date')->label('Issue Date')->date(),
                Tables\Columns\TextColumn::make('delivery_date')->label('Delivery Date')->date(),                
                Tables\Columns\TextColumn::make('customer.name')->label('Customer'), // Relación con Customer
                Tables\Columns\TextColumn::make('seller.name')->label('Seller'), // Relación con User
                Tables\Columns\TextColumn::make('reference_name')->label('Reference Name')->searchable(),
                Tables\Columns\TextColumn::make('total')->label('Total')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('classification.name')->label('Classification'), // Relación con QuestionCategory
                Tables\Columns\TextColumn::make('status')->label('Status')->sortable(),

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
                Tables\Actions\Action::make('Import Items')
               // ->icon('heroicon-o-upload')
                ->modalHeading('Import Order Items')
                ->modalSubheading('Enter each item on a new line in the format: Name, Quantity, Price.')
                ->form([
                    Forms\Components\TextArea::make('items')
                        ->label('Items')
                        ->placeholder("Example:\nItem 1, 2, 10\nItem 2, 3, 15")
                        ->required()
                        ->rows(6),
                ])
                ->action(function (Order $record, array $data) {
                    $items = explode("\n", $data['items']); // Separar por líneas
                    foreach ($items as $item) {
                        $fields = explode(',', $item); // Separar por comas
                        if (count($fields) !== 3) {
                            continue; // Saltar líneas con formato incorrecto
                        }

                        [$name, $quantity, $price] = array_map('trim', $fields);

                        $record->items()->create([
                            'name' => $name,
                            'quantity' => (int) $quantity,
                            'price' => (float) $price,
                        ]);
                    }
                })
                ->color('success')
                ->tooltip('Import items from a textarea')
                ,
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
            OrderItemRelationManager::class 
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
    
}
