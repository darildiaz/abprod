<?php

namespace App\Filament\Resources;
//namespace BezhanSalleh\FilamentShield\Resources;
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
use App\Filament\Resources\OrderResource\RelationManagers\OrderReferenceRelationManager;
use App\Filament\Resources\OrderResource\RelationManagers\OrderModelRelationManager;
use App\Filament\Resources\OrderResource\RelationManagers\OrderQuestionAnswerRelationManager;
use App\Filament\Resources\OrderResource\RelationManagers\ProductionRelationManager;
use App\Models\Question;
use App\Models\QuestionCategory;
use Illuminate\Support\Facades\DB;
use App\Models\Price;
use App\Models\Product;
use App\Models\Size;
use App\Models\Category;
use App\Models\user;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\TeamMember;
use App\Models\ClassCenter;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
class OrderResource extends Resource
implements HasShieldPermissions
{
    protected static ?string $model = Order::class;
    public static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    public static ?string $navigationGroup = 'Pedidos';
    public static ?string $recordTitleAttribute='id';
    protected static ?string $navigationLabel = 'Pedidos';
    protected static ?string $pluralLabel = 'Pedidos';

//    protected static ?string $navigationParentItem = 'Notifications';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status',1)->count();
    }
    Public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'status_production',
            'planning',
            'seller',
            'ver_todos'
        ];
    }
    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('General')
                        ->schema([
                            // Tab 1: Datos generales del cliente y pedido
                            Forms\Components\TextInput::make('bitrix_id'),
                            Forms\Components\TextInput::make('reference_name')
                                        ->label('Referencia')
                                       // ->default('ORD-'.date('Ymd').'-'.rand(1000,9999))
                                        ->required(),
                            Forms\Components\Select::make('customer_id')
                                        ->label('Cliente')
                                        ->relationship(
                                            name: 'customer',
                                            modifyQueryUsing: fn (Builder $query) => $query->orderBy('nif')->orderBy('name'),
                                        )

                                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "({$record->nif}) {$record->name} - {$record->phone}")
                                        ->searchable(['name', 'nif','phone'])
                                      //  ->default(1) // Predetermina el usuario logueado
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('nif')
                                            ->label('C.I.')
                                            ->required()
                                            ->maxLength(20)
                                            ->unique() // El NIF debe ser único
                                            ->placeholder('Ingrese el CI del cliente'),

                                        Forms\Components\TextInput::make('name')
                                            ->label('nombre')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Ingrese el nombre del cliente'),
                                        Forms\Components\TextInput::make('email')
                                                ->email()
                                                ->label('Correo electrónico'),
                                        Forms\Components\Textarea::make('address')
                                            ->label('Direccion')
                                            ->rows(3)
                                            ->required()

                                            ->placeholder('Ingrese la Ciudad del cliente'),

                                        Forms\Components\TextInput::make('phone')
                                            ->label('Celular')
                                            ->maxLength(15)
                                            ->tel() // Input con validación para números de teléfono
                                            ->placeholder('Ingrese el número de teléfono')
                                            ->required()
                                            ,
                                        Forms\Components\Hidden::make('user_id')
                                            ->default(auth()->id()) // Predetermina el usuario logueado
                                        ])
                                        ->required(),

                            Forms\Components\Select::make('seller_id')
                                        ->label('Vendedor')
                                        ->relationship('seller', 'name')
                                        ->default(auth()->id()) // Predetermina el usuario logueado
                                        ->live()
                                        ->afterStateUpdated(fn (callable $set, $state) =>
                                        $set('team_id', TeamMember::where('user_id', $state)->first()?->team_id)
                                            ) // Busca el `team_id` del vendedor seleccionado
                                        ->required(),
                            Forms\Components\Select::make('manager_id')
                                        ->label('Gestor')
                                        ->required()
                                        ->relationship('manager', 'name')
                                        ->default(auth()->id()) // Predetermina el usuario logueado
                                        ->live(),
                            Forms\Components\TextInput::make('issue_date')
                                        ->label('Fecha de emision')
                                        ->readOnly()
                                        ->default(today()->toDateString())
                                        ->type('date')
                                        ->required(),

                            Forms\Components\TextInput::make('delivery_date')
                                        ->label('Fecha de entrega')
                                ->afterStateHydrated(function ($state, callable $set) {
                                    if (fn():bool=>auth()->user()?->hasAnyRole(['super_admin'])) {
                                        $set('delivery_date', $state); // Bloquea cambios manteniendo el valor original
                                    }
                                })
                                ->readOnly(fn():bool=>!auth()->user()?->hasAnyRole(['super_admin']))
                                    ->default(today()->addDays(10)->toDateString())
                                    ->type('date')
                                    ->required(),

                            Forms\Components\Select::make('classification_id')
                                        ->label('Clasificacion')
                                        ->relationship('classification', 'name')
                                         ->default(1) // Predetermina el usuario logueado
                                         ->live()
                                        ->required(),
                            Forms\Components\Select::make('team_id')
                                        ->required()
                                        ->default(TeamMember::where('user_id', Auth()->id())->first()?->team_id
                                            )
                                        ->relationship('team', 'name')
                                        ->live(),

                    ]) ->columns(3),

                        Forms\Components\Wizard\Step::make('Items import')
                                ->label('Importar items')
                                ->schema([
                                    TableRepeater::make('Diccionario')
                                    //      ->cloneable()
                                    ->dehydrated(false)
                                    ->default([
                                        [
                                            'product_id' => 1,
                                            'product_name' => 'Camiseta',
                                        ],
                                        [
                                            'product_id' => 37,
                                            'product_name' => 'Short',
                                        ],
                                    ])
                              ->label('Order Items')
                            //->relationship('orderItems') // Relación con la tabla order_items
                              ->schema([

                                  Forms\Components\TextInput::make('product_name')
                                  ->dehydrated(false)
                                  ->label('Nombre del producto'),
                                  Forms\Components\Select::make('product_id')
                                    ->label('Producto')
                                    ->dehydrated(false)
                                    ->relationship('orderItems.product', 'code') // Relación con la tabla products

                                  ->searchable(),
                              ]),



                                //     // Tab 4: Cargar lista de ítems de la orden
                                //     Forms\Components\Toggle::make('order_items_orden')
                                //     ->default(true)
                                // ->dehydrated(false) // No se guarda en la base de datos

                                //     ->label('ordenar'),
                                //     Forms\Components\Toggle::make('order_items_hab_diccionario')

                                //     ->default(true)
                                // ->dehydrated(false) // No se guarda en la base de datos
                                // ->label('Habilitar Diccionario'),

                                    // Forms\Components\TextArea::make('order_items_diccionario')
                                    // ->placeholder("Producto\tCodigo\nCamiseta\tCam-f03\nShort\tSht-f03")
                                    // ->default("Producto\tCodigo\nCamiseta\tCam-f03\nShort\tSht-f03")

                                    // ->dehydrated(false) // No se guarda en la base de datos
                                    // ->rows(4)
                                    // ->live(onBlur: true),


                                    Forms\Components\TextArea::make('order_items_text')
                                        ->label('Pedido Items (Pegue Texto, Reemplazar texto sin el título)')
                                        ->placeholder("orden\tnombre\tnumero\totros\tcantidad\ttalle\tProducto\tPrecio\ttalle\tProducto\tPrecio\ttalle\tProducto\tPrecio\ttalle\tProducto\tPrecio\ttalle\tProducto\tPrecio\tsubtotal\ttotal
                                    1\tjugador1\t10\tg\t1\tm-cab\tCamiseta\t45000\tm-cab\tCamiseta\t45000\tm-cab\tshort\t45000\tm-cab\tmedia\t45000\tm-cab\tcamisilla\t45000\t225000\t225000
                                    2\tjugador2\t10\tg\t1\tm-cab\tCamiseta\t45000\tm-cab\tCamiseta\t45000\tm-cab\tshort\t45000\tm-cab\tmedia\t45000\tm-cab\tcamisilla\t45000\t225000\t225000
                                    3\tjugador3\t10\tg\t1\tm-cab\tCamiseta\t45000\tm-cab\tCamiseta\t45000\tm-cab\tshort\t45000\tm-cab\tmedia\t45000\tm-cab\tcamisilla\t45000\t225000\t225000")
                                        ->dehydrated(false) // No se guarda en la base de datos
                                        ->rows(8)
                                        ->helperText('Pegue los elementos del pedido separados por TAB para las columnas y ENTER para las filas.')
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state, $set, $get) {
                                            // Procesar el texto y actualizar el estado del Repeater
                                            $items = self::parseOrderItemsText($state, $set, $get);
                                            $set('orderItems', $items); // Actualiza el Repeater
                                        }),

                                // Forms\Components\TextArea::make('order_items_text_price')
                                // ->label('Pedido Items  con precio (Pegue Texto, Reemplazar texto sin el titulo)')
                                // ->placeholder("Item\tModelo\tNombre\tNúmero\tOtros\tTalle\tCantidad\tProductos\tPrecio\n1\tModelo 1\tJugador 1\t10\t0rh+\tm-cab\t1\tCamiseta\t85000\n1\t\t\t\t\t\t\tShort\t35000\n2\tModelo 1\tJugador 2\t10\t0rh+\tm-cab\t1\tCamiseta3\t85000\n2\t\t\t\t\t\t\tShort\t35000")
                                // ->default("Item\tModelo\tNombre\tNúmero\tOtros\tTalle\tCantidad\tProductos\tPrecio\n1\tModelo 1\tJugador 1\t10\t0rh+\tm-cab\t1\tCamiseta\t85000\n1\t\t\t\t\tm-cab\t1\tShort\t35000\n2\tModelo 1\tJugador 2\t10\t0rh+\tm-cab\t1\tcamiseta\t85000\n2\t\t\t\t\tm-cab\t1\tShort\t35000")
                                // ->rows(8)
                                // ->dehydrated(false) // No se guarda en la base de datos
                                // ->helperText('Pegue los elementos del pedido separados por TAB para las columnas y ENTER para las filas.')
                                // ->live(onBlur: true)
                                // ->afterStateUpdated(function ($state, $set, $get) {
                                //     // Procesar el texto y actualizar el estado del Repeater
                                //    // log::info('Order Items:', $state);
                                //     $items = self::parseOrderItemsTextprice($get('order_items_text_price'),$get('order_items_diccionario'), $set, $get);
                                //     $set('orderItems', $items); // Actualiza el Repeater
                                // }),
                                 ]),

                            Forms\Components\Wizard\Step::make('Items')
                                            ->schema([
                                                TableRepeater::make('orderItems')
                                          //      ->cloneable()
                                    ->label('Order Items')
                                    ->relationship('orderItems') // Relación con la tabla order_items
                                    ->schema([
                                        Forms\Components\Select::make('model')
                                        ->label('Modelo')
                                        ->options(collect(range(1, 20))->mapWithKeys(fn ($num) => ["MODELO $num" => "MODELO $num"]))
                                        ->required()
                                        ->default('MODELO 1')
                                        ->searchable(),
                                        Forms\Components\TextInput::make('item')
                                        ->readOnly()
                                        ->afterStateHydrated(function ($state, callable $set, callable $get) {
                                            // Si el estado aún no tiene valor, definirlo como el siguiente número disponible
                                            if (!$state) {
                                                $models = $get('../../orderItems') ?? []; // Obtener todos los modelos en el repeater
                                                $nextIndex = count($models) ; // Definir el próximo número
                                                $set('item', "$nextIndex"); // Asignar el valor predeterminado
                                            }
                                        })
                                            //->label('Item')
                                            ,
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nombre'),
                                        Forms\Components\TextInput::make('number')
                                            ->label('Numero'),
                                        Forms\Components\TextInput::make('other')
                                            ->label('otros'),
                                        Forms\Components\Select::make('size_id')
                                            ->label('Talle')
                                        ->default(1)

                                            ->live()
                                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getPrice($set, $get))
                                            ->relationship('size', 'name') // Relación con la tabla sizes
                                            ->required(),
                                        Forms\Components\TextInput::make('quantity')
                                            ->label('Cantidad')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->live(onBlur: true)
                                            // ->live()
                                        ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getPrice($set, $get))

                                          ,
                                            Forms\Components\TextInput::make('price')
                                            ->label('Precio')
                                         ->hiddenOn('view')

                                            ->readOnly()
                                            ->numeric()
                                           // ->disabled()
                                            ->default(0)
                                            ->live()
                                            ->required()
                                            ->afterStateUpdated(fn ($state, $set, $get) => self::updateSubtotal($set, $get)),
                                        Forms\Components\TextInput::make('subtotal')
                                            ->label('Subtotal')
                                         ->hiddenOn('view')

                                            ->readOnly()
                                            ->numeric()
                                            // ->disabled() // Deshabilita el campo para que no se pueda editar
                                            ->live()
                                            ->default(0)
                                            ->required(),


                                        Forms\Components\select::make('products_id')
                                            ->label('Productos')
                                            ->searchable()
                                            ->multiple()
                                            ->relationship('product', 'code')
                                            ->live()
                                            ->afterStateHydrated(function ($state, callable $set, callable $get) {
                                                // Obtener el índice actual del repeater
                                                $index = $get('../../index');
                                                // Obtener todos los items
                                                $items = $get('../../orderItems') ?? [];
                                                
                                                // Verificar si existe el item actual
                                                if (isset($items[$index])) {
                                                    $currentItem = $items[$index];
                                                    // Si existe product_id, decodificar y establecer los productos
                                                    if (isset($currentItem['product_id'])) {
                                                        $productIds = json_decode($currentItem['product_id'], true);
                                                        if (is_array($productIds)) {
                                                            $set('products_id', $productIds);
                                                        }
                                                    }
                                                }
                                            })
                                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getPrice($set, $get))

                                    ])
                                    ->columns(9)
                                    ->live()
                                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getTotal($set, $get))
                                     ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getRefences($set, $get))


                                    ->required()
                                    ,
                                    Forms\Components\TextInput::make('total')
                                        ->label('Total')
                                        ->readOnly()
                                        ->numeric()
                                        ->live() // Habilita la reactividad
                                        //->disabled() // Deshabilita el campo para que no se pueda editar
                                        ->default(0)
                                        ->numeric()
                                        ->required(),

                            // Tab 3: Cargar lista de referencias
                            TableRepeater::make('references')
                            ->reorderable()
                                ->collapsible()
                                ->label('References')
                                ->relationship('orderReferences') // Relación con la tabla order_references
                                ->schema([
                                    Forms\Components\TextInput::make('item')
                                        ->readOnly()

                                    ->default(1),
                                    Forms\Components\Hidden::make('product_id'),

                                    Forms\Components\Select::make('product_id')
                                        ->label('Producto')
                                         ->disabled()
                                        //->readOnly()

                                        ->relationship('product', 'code') // Relación con la tabla products
                                        ->default(1)
                                        ->required(),
                                        Forms\Components\Hidden::make('size_id'),
                                        Forms\Components\Select::make('size_id')
                                        ->label('Talle')
                                        ->default(1)
                                        //->readOnly()

                                         ->disabled()
                                        //->live()
                                        ->relationship('size', 'name') // Relación con la tabla sizes
                                        ->required(),
                                    Forms\Components\TextInput::make('quantity')
                                        ->label('Cantidad')
                                        // ->disabled()
                                        ->readOnly()

                                        ->numeric()
                                        ->required(),

                                        Forms\Components\TextInput::make('price')
                                        ->label('Precio')
                                        ->hiddenOn('view')

                                        // ->disabled()
                                        ->default(1)
                                        ->live(onBlur: true)

                                        ->numeric()
                                        ->required(),

                                ])
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getitems($set, $get)),
                                Forms\Components\TextInput::make('total')
                                        ->label('Total')
                                        ->hiddenOn('view')
                                        ->readOnly()
                                        ->numeric()
                                        ->live() // Habilita la reactividad
                                        ->default(0)
                                        ->numeric()
                                        ->required(),
                            ]),
                            Forms\Components\Wizard\Step::make('Questions')
                            ->label('Informacion adicional')

                            ->schema([
                                Forms\Components\Repeater::make('models')
                                ->label('Modelos')
                                ->relationship('orderMolds')
                                ->schema([
                                    Forms\Components\Select::make('title')
                                        ->label('Titulo')
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

                                    Forms\Components\FileUpload::make('imagen')->label('Imagen')
                                    ->required()
                                    ->image()
                                    ->directory('orders')
                                    ,
                                ])
                                ->columns(2)
                                ->required(),


                                TableRepeater::make('questionAnswers')
                               ->label('Questions')
                               ->relationship('questionAnswers')
                               ->schema([
                                    Forms\Components\Select::make('question_id')
                                    ->relationship('question','text')
                                    ->label('Cuestionario')
                                    ->live()
                                    ->afterStateHydrated(function ($state, callable $set, callable $get) {
                                        $record = $get('../../record');
                                        if ($record) {
                                            $questionAnswers = $record->questionAnswers;
                                            $index = $get('../../index');
                                            if (isset($questionAnswers[$index])) {
                                                $set('question_id', $questionAnswers[$index]->question_id);
                                                $set('answer', $questionAnswers[$index]->answer);
                                            }
                                        }
                                    })
                                    ->required(),
                                    Forms\Components\TextInput::make('answer')
                                    ->label('Respuesta')
                                    ->live()
                                    ->datalist(fn (callable $get) =>
                                        Question::where('id', $get('question_id'))
                                            ->pluck('options') // Récupère la colonne JSON
                                            ->flatMap(fn ($options) => json_decode($options, true)) // Décode et aplatit le tableau
                                            ->mapWithKeys(fn ($option) => [$option => $option]) // Formate les clés/valeurs
                                    )
                                    ->required(),
                                    ])

                               ])

                            ])->columnSpan('full')
                            ->afterStateHydrated(function (callable $set, callable $get) {
                                $classificationId = $get('classification_id');
                                self::getQuestionFields($classificationId,$set);
                            })


            ]);
    }

    public static function table(Table $table): Table
    {
        $categories = Category::all();
        return $table
           ->groups([
                    Group::make('classification.name')
                    ->label('Clasificacion')
                    ->collapsible(),
                    Group::make('delivery_date')
                    ->label('Fecha de entrega')
                    ->collapsible(),

                    Group::make('issue_date') // Doit être une chaîne, pas une Closure
                    ->label('Mes de Emisión')
                    ->getTitleFromRecordUsing(fn ($record) => Carbon::parse($record->issue_date)->translatedFormat('F Y'))
                    ->collapsible(),
                ])
            ->defaultSort('id', 'desc')
            ->query(fn () => auth()->user()->can('ver_todos_order')
                ? Order::query() // Si es admin, muestra todos los pedidos
                : Order::query()->where('manager_id', auth()->id()) // Si no, filtra por manager_id
                )
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('bitrix_id')->searchable(),
                Tables\Columns\TextColumn::make('issue_date')->label('Fecha de emision')->date(),
                Tables\Columns\TextColumn::make('delivery_date')->label('Fecha de entrega')->date(),
                Tables\Columns\TextColumn::make('completion_date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('shipping_date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('customer.name')->label('Cliente'), // Relación con Customer
                Tables\Columns\TextColumn::make('seller.name')->label('Vendedor'), // Relación con User
                Tables\Columns\TextColumn::make('manager.name')->label('Gestor'), // Relación con User
                Tables\Columns\TextColumn::make('reference_name')->label('Referencia')->searchable(),
                Tables\Columns\TextColumn::make('total')->label('Total')->money('Gs.')
                ->visible(fn () => auth()->user()->can('seller_order'))
                ->summarize(Sum::make())
                ->sortable(),
                Tables\Columns\TextColumn::make('PaymentHistories.amount')->label('Monto Recibido')
              //  ->summarize('PaymentHistories.amount')
              ->visible(fn () => auth()->user()->can('seller_order'))

              ->state(function (Model $record): int {
                return  $record->paymentHistories()->where('status', true)->sum('amount');
            })
              ->money('Gs.'),
                Tables\Columns\TextColumn::make('Saldo')->label('Saldo')
                ->visible(fn () => auth()->user()->can('seller_order'))

                ->state(function (Model $record): int {
                    return $record->total - $record->paymentHistories()->where('status', true)->sum('amount');
                })
              ->money('Gs.')
                ,

                Tables\Columns\TextColumn::make('classification.name')->label('Clasificacion'), // Relación con QuestionCategory
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            0 => 'Pendiente',
                            1 => 'Planificado',
                            2 => 'Completado',
                            3 => 'Enviado',
                            4 => 'Cancelado',
                            default => 'Desconocido',
                        };
                    }),
                Tables\Columns\TextColumn::make('productions')
                    ->label('Center')
                    ->formatStateUsing(function ($state, $record) {
                        $maxCenter = collect($record->productions)
                            ->map(fn ($production) => $production->center)
                            ->sortByDesc('level')
                            ->first();

                        return $maxCenter ? $maxCenter->name : '';
                    }),

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
                Tables\Filters\SelectFilter::make('customer.name')
                ->label('Cliente')
                // Mostrar el nombre del vendedor

                ->relationship('customer', 'name'),
                Tables\Filters\SelectFilter::make('status')
                ->multiple()
                ->options([
                        0 => 'Pendiente',
                        1 => 'Planificado',
                        2 => 'Completo',
                        3 => 'Enviado',
                ]),
                Tables\Filters\SelectFilter::make('seller.name')
                    ->relationship('seller', 'name')
                    ,
                    Tables\Filters\SelectFilter::make('manager.name')
                    ->relationship('manager', 'name')
                 //->hidden(fn () => auth()->user()->can('ver_todos'))

                 ->default(auth()->id())
                    ,

            ])
            ->actions([

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('paymenthistory')
                ->label('Pagos')
                ->form([
                    Forms\Components\Repeater::make('paymenth')
                    ->label('Planificación')
                    ->relationship('paymenthistories') // Relación con la tabla order_references
                    ->schema([
                            Forms\Components\DatePicker::make('date')
                            ->label('Fecha')
                                        ->default(now())
                                        ->required(),
                            Forms\Components\TextInput::make('amount')

                            ->label('Monto')
                            ->required()
                                ->suffix('Gs.')
                                ->numeric(),
                            Forms\Components\Select::make('payment_method_id')
                            ->label('Metodo de pago')
                                ->relationship('paymentMethod', 'name')
                                ->required(),
                            Forms\Components\TextInput::make('reference')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\FileUpload::make('image')->label('Image')
                            ->label('Comprobante')
                            ->directory('pay')
                            ->required(),
                            Forms\Components\Hidden::make('seller_id')
                                ->default(auth()->id()) // Predetermina el usuario logueado
                                 ->required()
                    ])
                    ->columns(3)
                ]),
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    //->icon('heroicon-o-archive')
                    ->action(function (Model $record) {
                        return response()->streamDownload(function () use ($record) {
                            foreach ($record->orderItems as $item) {
                                $item->type = DB::table('order_references')
                                    ->join('products', 'order_references.product_id', '=', 'products.id')
                                    ->join('categories', 'products.category_id', '=', 'categories.id')
                                    ->join('sizes', 'order_references.size_id', '=', 'sizes.id') // Asumimos que hay una tabla sizes
                                    ->select(DB::raw('CONCAT(categories.name, " (", sizes.name, ")") as category_size'))
                                    ->where('order_references.order_id', $record->id)
                                    ->where('order_references.item', $item->item)
                                    ->pluck('category_size') // Extraer el resultado concatenado
                                    ->implode(' + '); // Unir todos los resultados con "+"
                            }
                            foreach ($record->orderMolds as $model) {
                                $qrCode = QrCode::size(100)->generate( asset('storage/' . $model->imagen ?? 'N/A' ));
                                // Convertir el QR a Base64 para insertarlo en el PDF
                                $qrCodeBase64 = base64_encode($qrCode);

                                // Asignar el QR Base64 al modelo para pasarlo al Blade
                                $model->qr = 'data:image/svg+xml;base64,' . $qrCodeBase64;
                            }
                            echo Pdf::loadHtml(
                                Blade::render('pdf.invoice', ['order' => $record])
                            )->stream();
                        }, $record->id . ' Pedido.pdf');
                    }),
                Tables\Actions\Action::make('changeStatus')
                ->visible(fn ($record) => $record->status < 2)
                ->visible(fn () => auth()->user()->can('status_production_order'))
                ->label('editar estado')
                ->action(function ($record, $data) {
                    $record->status = $data['status'];
                    // Actualizar fechas según el estado seleccionado
                    if ($data['status'] == 2) {
                        $record->completion_date = now();
                    } elseif ($data['status'] == 3) {
                        $record->shipping_date = now();
                    }
                    $record->save();
                })
                ->form([
                    Forms\Components\Select::make('status')
                        ->options([
                            1 => 'Planificado',
                            2 => 'Completado',
                            3 => 'Enviado',
                            4 => 'Cancelado',
                        ])
                        ->required(),
                        ])->requiresConfirmation(),
                Tables\Actions\Action::make('planning1')
                ->visible(fn () => auth()->user()->can('planning_order'))
                ->action(function ($record) {
                    $record->status = 1;

                    $record->save();
                })
                ->label('Planificar')
                ->form([
                    Forms\Components\Section::make('plannings')
                            ->label('Informacion adicional')

                            ->afterStateHydrated(function ($record, callable $set) {
                                if (!$record) return;
                                // Obtener fechas base
                                $issueDate = Carbon::parse($record->issue_date)->addDay(); // issue_date + 1 día
                                $deliveryDate = Carbon::parse($record->delivery_date)->subDay(); // delivery_date - 1 día
                                // Obtener los centros ordenados por `item`
                                $centers = ClassCenter::where('category_id', $record->classification_id)
                                    ->orderBy('item') // Ordenar por `item` para respetar el flujo de trabajo
                                    ->get()
                                    ->groupBy('item'); // Agrupar por `item` para manejar centros en paralelo
                                // Distribuir fechas entre los grupos de `item`
                                $items = $centers->keys(); // Obtener los `items` únicos
                                $totalSteps = $items->count();
                                $planning = [];

                                if ($totalSteps > 0) {
                                    $dateStep = $totalSteps > 1 ? $issueDate->diffInDays($deliveryDate) / ($totalSteps - 1) : 0;

                                    foreach ($items as $index => $item) {
                                        $currentDate = $issueDate->copy()->addDays(round($index * $dateStep))->toDateString();

                                        // Asignar la misma fecha a todos los centros con el mismo `item`
                                        foreach ($centers[$item] as $center) {
                                            $planning[] = [
                                                'date' => $currentDate,
                                                'center_id' => $center->center_id,
                                            ];
                                        }
                                    }
                                }

                                // Establecer los datos en el formulario
                                $set('planning', $planning);
                            })
                            ->schema([
                                Forms\Components\Repeater::make('planning')
                                    ->label('Planificación')
                                    ->relationship('planning') // Relación con la tabla order_references
                                    ->schema([
                                        Forms\Components\DatePicker::make('date')->required(),
                                        Forms\Components\Select::make('center_id')
                                            ->label('Centro')
                                            ->relationship('center', 'name') // Relación con el modelo Center
                                            ->required(),
                                    ])
                                    ->columns(2)
                            ])
                        ])

                ->requiresConfirmation(),



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
            OrderItemRelationManager::class,
            OrderReferenceRelationManager::class,
            OrderModelRelationManager::class,
            OrderQuestionAnswerRelationManager::class,
            ProductionRelationManager::class
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
    protected static function getQuestionFields(?int $classificationId,callable $set)
{
    // Si no hay clasificación, devolver un array vacío
         if ($classificationId) {


            // Obtener las preguntas relacionadas con esta clasificación
            $questions = Question::where('category_id', $classificationId)->get(['id']);

            // Inicializar un array para los campos dinámicos
            $fields = [];

            foreach ($questions as $question) {
                $fields[] = [
                    'question_id' =>$question->id
                ];
            }
            $set('questionAnswers',$fields);
     }
}
protected static function getitems(callable $set, callable $get)
{
$references=$get('references');
$itemsorg = $get('orderItems') ;
//Log::info('Order Items:', $references->toarray());
$items = [];
$prod=[];
$total = 0;
foreach ($itemsorg as $item) {
    $price = 0;

    foreach ($references as $ref) {
        if ($ref['item'] == $item['item']) {
            $prod[]= $ref['product_id'];
            $price = $price + $ref['price'];
        }
    }
        $items[] = [
            'item' => $item['item'],
            'model' => $item['model'],
            'name' => $item['name'],
            'number' => $item['number'],
            'other' => $item['other'],
            'size_id' => $item['size_id'],
            'quantity' => $item['quantity'],
            'products_id' => $prod,
            'price' => $price,
            'subtotal' => $price * $item['quantity'],
        ];
$prod=[];

    $total = $total + $price * $item['quantity'];
}

$set('orderItems', $items);
$set('total', $total);

}


protected static function getRefences(callable $set, callable $get)
{
    $item = $get('orderItems') ;
    $refences = [];
    $c=0;
    $total=0;
    foreach ($item as $item) {
        $c++;
        foreach ($item['products_id'] as $product) {

                $refences[] = [
                    'item' => $c,
                    'product_id' => $product,
                    'size_id' => $item['size_id'],
                    'quantity' => $item['quantity'],
                    'price' => self::getPPrice($product, $item['size_id']),
                    'subtotal' => ($item['quantity'] * self::getPPrice($product, $item['size_id'])),
                ];
                $total=$total+($item['quantity'] * self::getPPrice($product, $item['size_id']));

        }
    }
    $set('total', $total);
    $set('references', $refences);
}
protected static function parseOrderItemsText(string $text, $set, $get): array
{
    $odr = $get('order_items_odr');
    $proddirc = $get('Diccionario');
    $references = [];
    $items = [];
    $total = 0;
    $c = 0;

    // Convertir las líneas en arrays asociativos
    $lines = array_map(function ($line) {
        $columns = explode("\t", trim($line));

        return [
            'id' => $columns[0] ?? '',
            'model' => $columns[1] ?? '',
            'name' => $columns[2] ?? '',
            'number' => $columns[3] ?? '',
            'other' => $columns[4] ?? '',
            'quantity' => (int) ($columns[5] ?? 1), // Cantidad por defecto es 1 si no está definida
            'products' => array_filter([
                ['size' => $columns[6] ?? '', 'name' => $columns[7] ?? '', 'price' => (int) ($columns[8] ?? 0)],
                ['size' => $columns[9] ?? '', 'name' => $columns[10] ?? '', 'price' => (int) ($columns[11] ?? 0)],
                ['size' => $columns[12] ?? '', 'name' => $columns[13] ?? '', 'price' => (int) ($columns[14] ?? 0)],
                ['size' => $columns[15] ?? '', 'name' => $columns[16] ?? '', 'price' => (int) ($columns[17] ?? 0)],
                ['size' => $columns[18] ?? '', 'name' => $columns[19] ?? '', 'price' => (int) ($columns[20] ?? 0)],
            ], fn($product) => !empty($product['name'])) // Filtrar productos vacíos
        ];
    }, explode("\n", trim($text)));

    // Validar que cada línea tenga al menos un producto
    foreach ($lines as &$line) {
        if (empty($line['products'])) {
            throw new \Exception("Cada línea debe tener al menos un producto obligatorio.");
        }
    }

    // Ordenar por productos y talle si es necesario
    if ($odr) {
        usort($lines, function ($a, $b) {
            return strcmp(
                implode(',', array_column($b['products'], 'name')),
                implode(',', array_column($a['products'], 'name'))
            ) ?: $a['products'][0]['size'] <=> $b['products'][0]['size'];
        });
    }

    // Procesar cada línea después de ordenar
    foreach ($lines as $line) {
        $c++;
        $lineTotal = 0;
        $productIds = []; // Contendrá solo los IDs de productos

        // Obtener el primer talle de la lista de productos
        $firstSize = $line['products'][0]['size'] ?? '';

        // Obtener IDs de productos y calcular precios
        foreach ($line['products'] as $product) {
            //$productId = self::getProductIdByName(trim($product['name']));
            $productId= null;
            foreach ($proddirc as $dicc) {
                log::info('Diccionario:', $dicc);
                if(strtolower($dicc['product_name']) == strtolower($product['name'])){
                    $productId=$dicc['product_id'];
                    break; // Salir del bucle si se encuentra una coincidencia
                } 
            }
            $sizeId = self::getSizeIdByName(trim($product['size']));
            $unitPrice = $product['price'] ?? 0;
            
            if($unitPrice==0){
                $unitPrice = self::getPPrice($productId, $sizeId);
            }

            $productIds[] = $productId; // Guardamos solo IDs de productos
            $lineTotal += $unitPrice; // Sumar precio del producto

            // Agregar a referencias
            $references[] = [
                'item' => $c,
                'product_id' => $productId,
                'size_id' => $sizeId,
                'quantity' => $line['quantity'],
                'price' => $unitPrice,
                'subtotal' => $line['quantity'] * $unitPrice,
            ];
        }

        // Calcular subtotal y total
        $subtotal = $lineTotal * $line['quantity'];
        $total += $subtotal;

        // Agregar datos al array de items
        $items[] = [
            'item' => $c,
            'model' => $line['model'],
            'name' => $line['name'],
            'number' => $line['number'],
            'other' => $line['other'],
            'size_id' => self::getSizeIdByName($firstSize), // Ahora usamos el primer size
            'quantity' => $line['quantity'],
            'products_id' => $productIds, // Ahora es un array plano
            'price' => $lineTotal,
            'subtotal' => $subtotal,
        ];
    }

    // Asignar los valores finales
    $set('references', $references);
    $set('total', $total);

    return $items;
}



protected static function parseOrderItemsTextprice(string $text,string $dicctext,  $set,$get): array
{
    // $hab=false;

    // $hab=$get('order_items_hab_diccionario');

    $diccss=[];
    $references = [];
    $items = [];
    $total = 0;
    $item1 = true;

    // Convertir las líneas en arrays asociativos
    // if($hab){
        //$dicctext = $get('order_items_diccionario');
        $Ddiccs= array_map(function($diccs){
            $columnDs = explode("\t", trim($diccs));
            return[
                    'product_name' => $columnDs[0] ?? '',
                    'code' => $columnDs[1] ?? '',
            ];

        }, explode("\n", trim($dicctext)));

    // }
    $lines = array_map(function ($line) {
        $columns = explode("\t", trim($line));
        $nombreprod=$columns[7] ?? '';

        return [
            'id' => (int) ($columns[0] ?? 0),
            'model' => $columns[1] ?? '',
            'name' => $columns[2] ?? '',
            'number' => $columns[3] ?? '',
            'other' => $columns[4] ?? '',
            'quantity' => (int) ($columns[5] ?? 0),
            'size1' => $columns[6] ?? '',
            'product1' => $nombreprod, // Convertir productos en array
            'price1' => (int) ($columns[8] ?? 0) , // Convertir productos en array
            'size2' => $columns[9] ?? '',
            'product2' => $nombreprod, // Convertir productos en array
            'price2' => (int) ($columns[11] ?? 0) , // Convertir productos en array
            'size3' => $columns[12] ?? '',
            'product3' => $nombreprod, // Convertir productos en array
            'price3' => (int) ($columns[14] ?? 0) , // Convertir productos en array
            'size4' => $columns[15] ?? '',
            'product4' => $nombreprod, // Convertir productos en array
            'price4' => (int) ($columns[17] ?? 0) , // Convertir productos en array
            'size5' => $columns[18] ?? '',
            'product5' => $nombreprod, // Convertir productos en array
            'price5' => (int) ($columns[20] ?? 0) , // Convertir productos en array
        ];
    }, explode("\n", trim($text)));



    // Procesar cada línea después de ordenar
    foreach ($lines as $line) {
        $item1 = true;
        $productIds = [];
        // Obtener IDs de productos
         // if($hab){
            foreach ($Ddiccs as $dicc) {
                if($dicc['product_name']==$line['products']){
                    $nombreprod=$dicc['code'];
                    break; // Salir del bucle si se encuentra una coincidencia
                } else {
                    $nombreprod=$line['products'];
                }
            }
        // }
        $productId = self::getProductIdByName(trim($nombreprod, ' '));
        // Calcular precios
            $references[] = [
                'item' => $line['id'],
                'product_id' => $productId,
                'size_id' => self::getSizeIdByName($line['size']),
                'quantity' => $line['quantity'],
                'price' => $line['price'],
                'subtotal' => $line['quantity'] * $line['price'],
            ];

        // Calcular subtotal y total
        $subtotal =$line['price'] * $line['quantity'];
        $total += $subtotal;

        // Agregar datos al array de items

            $c=0;
        foreach ($items as $item) {

            if ($c+1== $line['id']) {
                $items[$c]['products_id'][] = $productId;
                $items[$c]['price'] = $item['price'] + $line['price'];
                $items[$c]['subtotal'] = $items[$c]['price'] * $line['quantity'];
                $item1=false;


                // $item['products_id'][] = $productId;
                // $item['price'] = $item['price'] + $line['price'];
                // $item['subtotal'] = $item['price'] * $line['quantity'];
                // $item1=false;
            }
            $c++;
        }
        if($item1){
            $item1=true;
            $productIds[] = $productId;
            $items[] = [
                'item' => (int)($line['id']),
                'model' => $line['model'],
                'name' => $line['name'],
                'number' => $line['number'],
                'other' => $line['other'],
                'size_id' => self::getSizeIdByName($line['size']),
                'quantity' => $line['quantity'],
                'products_id' => $productIds,
                'price' =>  $line['price'],
                'subtotal' => $line['price'] * $line['quantity'],
            ];
        }

    }

    // Asignar los valores finales
    $set('references', $references);
    $set('total', $total);

    return $items;
}
    private static function getPrice(callable $set, callable $get)
    {
        $productIds = $get('products_id'); // Es un array porque es múltiple
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
        $total = 0;

        if ($quantity && $price) {
            $subtotal = $price * $quantity;
            $set('subtotal', $subtotal );
            $total = $total + $subtotal;
            $set('total', $total );
        }
    }
    protected static function getTotal($set, $get): void
    {
        $items = $get('orderItems') ;
        $total = 0;
        foreach ($items as $item) {
            $total = $total + $item['subtotal'];
        }
        $set('total', $total );
    }



    protected static function getproductIdByName(?string $sizeName): ?int
    {
        return Product::where('code', $sizeName)->value('id');
    }

    protected static function getSizeIdByName(?string $sizeName): ?int
    {
        return Size::where('name', $sizeName)->value('id');
    }

}
