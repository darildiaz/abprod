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
use App\Models\Question;
use App\Models\QuestionCategory;
use Illuminate\Support\Facades\DB;
use App\Models\Price;
use App\Models\Product;
use App\Models\Size;
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
                            Forms\Components\TextInput::make('reference_name')
                                        ->label('Reference Name')
                                        ->default('ORD-'.date('Ymd').'-'.rand(1000,9999))
                                        ->required(),
                            Forms\Components\Select::make('customer_id')
                                        ->label('Customer')
                                        ->relationship('customer', 'name')
                                        ->default(1) // Predetermina el usuario logueado
                                        ->required(),

                            Forms\Components\Select::make('seller_id')
                                        ->label('Seller')
                                        ->relationship('seller', 'name')
                                        ->default(auth()->id()) // Predetermina el usuario logueado
                                        ->required(),

                            Forms\Components\TextInput::make('issue_date')
                                        ->label('Issue Date')
                                        ->default(today()->toDateString())
                                        ->type('date')
                                        ->required(),

                            Forms\Components\TextInput::make('delivery_date')
                                        ->label('Delivery Date')
                                        //->default(today()->toDateString())
                                        ->default(today()->addDays(10)->toDateString())
                                        ->type('date')
                                        ->required(),

                            Forms\Components\Select::make('classification_id')
                                        ->label('clasificacion')
                                        ->relationship('classification', 'name')
                                         ->default(1) // Predetermina el usuario logueado
                                         ->live()
                                        ->required(),
                            Forms\Components\TextInput::make('Code discount')
                                        ->label('Code discount')
                                        ->default('0')
                                        ->dehydrated(false)
                                        ->required(),
                            Forms\Components\TextInput::make('aproved for')
                                        ->default('0')
                                        ->disabled()
                                        ->dehydrated(false),
                                        Forms\Components\TextInput::make('monto maximo')
                                        ->default('0')
                                        ->disabled()

                                        ->dehydrated(false),
                            
                    ]) ->columns(3),
                    
                    Forms\Components\Wizard\Step::make('Models')
                    ->schema([
                    // Tab 2: Cargar lista de modelos
                    Forms\Components\Repeater::make('models')
                    ->label('Models')
                    ->relationship('orderMolds')
                    ->schema([
                        Forms\Components\Select::make('title')
                            ->label('Modelo Title')
                            ->options(collect(range(1, 20))->mapWithKeys(fn ($num) => ["MODELO $num" => "MODELO $num"]))
                            ->required()
                            ->searchable()
                            ->afterStateHydrated(function ($state, callable $set, callable $get) {
                                // Si el estado aún no tiene valor, definirlo como el siguiente número disponible
                                if (!$state) {
                                    $models = $get('../../models') ?? []; // Obtener todos los modelos en el repeater
                                    $nextIndex = count($models) ; // Definir el próximo número
                                    $set('title', "MODELO $nextIndex"); // Asignar el valor predeterminado
                                }
                            }),
                            
                        Forms\Components\FileUpload::make('imagen')->label('Image'),
                    ])
                    ->columns(2)
                    ->required(),
                        ]),
                        Forms\Components\Wizard\Step::make('Items import')
                                ->schema([
                                    // Tab 4: Cargar lista de ítems de la orden
                                    Forms\Components\TextArea::make('order_items_text')
                                ->label('Order Items (Paste Text)')
                                ->placeholder("Item\tModelo\tNombre\tNúmero\tOtros\tTalle\tCantidad\tProductos\n1\tOficial\tJugador 1\t10\t0rh+\tm-cab\t1\tCamiseta,Short\n2\tArquero\tJugador 2\t1\t0rh+\tg-cab\t1\tCamiseta,Short")
                                ->rows(8)
                                ->dehydrated(false) // No se guarda en la base de datos
                                ->helperText('Paste order items separated by TAB for columns and ENTER for rows.')
                                ->live()
                                ->afterStateUpdated(function ($state, $set) {
                                    // Procesar el texto y actualizar el estado del Repeater
                                    $items = self::parseOrderItemsText($state, $set);
                                    $set('orderItems', $items); // Actualiza el Repeater
                                }),
                                        ]),
                            Forms\Components\Wizard\Step::make('Items')
                                            ->schema([
                                Forms\Components\Repeater::make('orderItems')
                                    ->label('Order Items')
                                    ->relationship('orderItems') // Relación con la tabla order_items
                                    ->schema([
                                        Forms\Components\Select::make('model')
                                        ->label('Modelo')
                                        ->options(collect(range(1, 20))->mapWithKeys(fn ($num) => ["MODELO $num" => "MODELO $num"]))
                                        ->required()
                                        ->searchable(),
                                        Forms\Components\TextInput::make('name')
                                            ->label('Name'),
                                        Forms\Components\TextInput::make('number')
                                            ->label('Number'),
                                        Forms\Components\TextInput::make('other')
                                            ->label('Other'),
                                        Forms\Components\Select::make('size_id')
                                            ->label('Size')
                                            ->live()
                                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getPrice($set, $get))
                                            ->relationship('size', 'name') // Relación con la tabla sizes
                                            ->required(),
                                        Forms\Components\TextInput::make('quantity')
                                            ->label('Quantity')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn ($state, $set, $get) => self::updateSubtotal($set, $get))
                                                                                  // ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getRefences($set, $get))
                                            ,                                       
                                            Forms\Components\TextInput::make('price')
                                            ->label('price')
                                            
                                            ->numeric()
                                           // ->disabled()
                                            ->default(0)
                                            ->live()
                                            ->required(),
                                           // ->afterStateUpdated(fn ($state, $set, $get) => self::updateSubtotal($set, $get)),
                                        Forms\Components\TextInput::make('subtotal')
                                            ->label('Subtotal')
                                            ->numeric()
                                            // ->disabled() // Deshabilita el campo para que no se pueda editar
                                            ->live()
                                            ->default(0)
                                            ->required(),
                                        
                                        
                                        Forms\Components\select::make('ProductsItem')
                                            ->label('Products')
                                            ->searchable()
                                            ->multiple()
                                            ->relationship('product', 'code') // Relación con la tabla products
                                        //    ->live() // Habilita la reactividad
                                        ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getPrice($set, $get))
                                        
                                    ])
                                    ->columns(9)
                                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getTotal($set, $get))
                                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getRefences($set, $get))

                                    
                                    ->required(),
                                    Forms\Components\TextInput::make('total')
                                        ->label('Total')
                                        ->live() // Habilita la reactividad
                                        //->disabled() // Deshabilita el campo para que no se pueda editar
                                        ->default(0)
                                        ->numeric()
                                        ->required(),

                                ])
                                ,
                                Forms\Components\Wizard\Step::make('References')
                        ->schema([
                            // Tab 3: Cargar lista de referencias
                                Forms\Components\Repeater::make('references')
                                ->label('References')
                                ->relationship('orderReferences') // Relación con la tabla order_references
                                ->schema([
                                                                           
                                    Forms\Components\Select::make('product_id')
                                        ->label('Product')
                                        // ->disabled()

                                        ->relationship('product', 'code') // Relación con la tabla products
                                        ->default(1)
                                        ->required(),
                                        Forms\Components\Select::make('size_id')
                                        ->label('Size')
                                        // ->disabled()

                                        //->live()
                                        ->relationship('size', 'name') // Relación con la tabla sizes
                                        ->required(),
                                    Forms\Components\TextInput::make('quantity')
                                        ->label('Quantity')
                                        // ->disabled()

                                        ->numeric()
                                        ->required(),
                                    
                                        Forms\Components\TextInput::make('price')
                                        ->label('Price')
                                        // ->disabled()
                                        ->numeric()
                                        ->required(),
                                        /* Forms\Components\TextInput::make('discount')
                                        ->label('discount')
                                        ->numeric()
                                        ->required(), */
                                        Forms\Components\TextInput::make('subtotal')
                                        ->numeric()
                                        ->disabled()
                                        , // Deshabilita el campo para que no se pueda editar
                                        
                                ])
                                ->columns(5)
                                ->live() // Habilita la reactividad
                                
                                //->required()
                                ,
                            ]),
                              /*  Forms\Components\Wizard\Step::make('Questions')
                                    ->schema([
                                        Forms\Components\Repeater::make('questionAnswers')
                                            ->label('Questions')
                                            ->relationship('questionAnswers')
                                            ->schema(fn ($get) => self::getQuestionFields($get('classification_id')))
                                            ->hidden(fn ($get) => !$get('classification_id')),
                  
                            ]),*/
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
                //Tables\Columns\TextColumn::make('status')->label('Status')->sortable(),

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
            OrderItemRelationManager::class 
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),

            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }


    
    protected static function getQuestionFields(?int $classificationId): array
{
    if (!$classificationId) {
        return [];
    }

    $questions = Question::where('category_id', $classificationId)->get();

    return $questions->map(function ($question) {
        // Decodificar las opciones desde el campo JSON
        $options = json_decode($question->options, true);

        // Verificar si las opciones son un array válido
        if (!is_array($options)) {
            $options = []; // Si no es un array válido, usar un array vacío
        }

        // Crear el campo correspondiente según el tipo de pregunta
        return match ($question->type) {
            'string' => Forms\Components\TextInput::make("answers.{$question->id}")
                ->label($question->text)
                ->required($question->is_required),

            'integer' => Forms\Components\TextInput::make("answers.{$question->id}")
                ->label($question->text)
                ->numeric()
                ->required($question->is_required),

            'list' => Forms\Components\Select::make("answers.{$question->id}")
                ->label($question->text)
                ->options($options) // Usar las opciones decodificadas
                ->required($question->is_required),

            default => null,
        };
    })->filter()->toArray();
}
protected static function getRefences(callable $set, callable $get)
{
    $item = $get('orderItems') ;
    $refences = [];
    $no=true;
    foreach ($item as $item) {        
        foreach ($item['ProductsItem'] as $product) {
            $no=true;
        /*    foreach ($refences as $index => $refence) {
                if ($refence['product_id'] == $product && $refence['size_id'] == $item['size_id']) {
                    // Actualizar cantidad y subtotal si se encuentra una referencia existente
                    $refences[$index]['quantity'] += $item['quantity'];
                    $refences[$index]['subtotal'] = $refences[$index]['quantity'] * $refences[$index]['price'];
                    $no = false;
                    break; // Salir del bucle una vez que se encuentra la referencia
                } 
            }
          */  if ($no) {
                $refences[] = [
                    'product_id' => $product,
                    'size_id' => $item['size_id'],
                    'quantity' => $item['quantity'],
                    'price' => self::getPPrice($product, $item['size_id']),
                    'subtotal' => $item['subtotal'],
                ];
            }
        }
    }
    $set('references', $refences);
}
protected static function parseOrderItemsText(string $text,$set): array
    {
        $items = [];
        $price=0;
        $lines = explode("\n", trim($text)); // Divide el texto en líneas
        $total=0;
        foreach ($lines as $line) {
            $columns = explode("\t", trim($line)); // Divide cada línea en columnas
            if (count($columns) === 8) { // Asegúrate de que haya 9 columnas
                $price=0;
                $productIds = [];
                foreach (explode(',', $columns[7]) as $code) {
                    $ids = self::getProductIdByName(trim($code)); // Obtener IDs de productos por código
                    if (!empty($ids)) {
                        $productIds[] = $ids; // Convertir en array y fusionar
                    }
                }
                foreach ($productIds as $productId) {
                    $price=$price+  self::getPPrice($productId, self::getSizeIdByName($columns[5]));
                }
                $items[] = [
                    'model' => $columns[1], // Obtener el ID del modelo
                    'name' => $columns[2],
                    'number' => $columns[3],
                    'other' => $columns[4],
                    'size_id' => self::getSizeIdByName($columns[5]), // Obtener el ID del talle
                    'quantity' => (int) $columns[6],
                    'ProductsItem' =>$productIds, // Convertir productos en un array
                   
                    'price' => (int) $price,
                    'subtotal' => (int) $price * (int) $columns[6],
                    $total=$total+(int) $price * (int) $columns[6],
                   // 'price' => self::getSUBTOTAL($columns[6],explode(',', $columns[7])),
                ];
            }
        }
        $set('total', $total );

        return $items;
    }
    private static function getPrice(callable $set, callable $get)
    {
        $productIds = $get('ProductsItem'); // Es un array porque es múltiple
        $sizeId = $get('size_id');

        if (!empty($productIds) && $sizeId) {
            $price = 0;
            $id=0;
            foreach ($productIds as $productId) {
                $price=$price+  self::getPPrice($productId, $sizeId);
            }
            $set('price', $price ?: 0);
        self::updateSubtotal($set, $get);
            
        } else {
            $set('price', 0);
        }
    }
    private static function getPPrice($productId, $sizeId): int
    {
        $price = Price::where('product_id', $productId)
            ->where('size_id', $sizeId)
            ->value('price');
        // Si no hay precio para el tamaño seleccionado, buscar el tamaño 1 (Normal)
        if (!$price) {
            $price = Price::where('product_id', $productId)
                ->where('size_id', 1)
                ->value('price');
        }

        return $price ?: 0;

    }

    protected static function updateSubtotal($set, $get): void
    {

        $quantity = $get('quantity') ?? 1;
        $price = $get('price') ?? 1;
        $subtotal = 0;
        if ($quantity && $price) {
            $subtotal = $price * $quantity;
            $set('subtotal', $subtotal );
            
        }
    }
    protected static function getTotal($set, $get): void
    {
        $item = $get('orderItems') ;
        $total = 0;
        foreach ($item as $item) {
            $total = $total + $item['subtotal'];
        }
        $set('total', $total );
    }
    protected static function getproductIdByName(?string $sizeName): ?int
    {
        return Product::where('code', $sizeName)->value('id');
      //return 1;
    }
    

    protected static function getSizeIdByName(?string $sizeName): ?int
    {
        return Size::where('name', $sizeName)->value('id');
    }
    
}
