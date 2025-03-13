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
        return static::getModel()::where('status',0)->count();
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

                                        Forms\Components\Textarea::make('address')
                                            ->label('Direccion')
                                            ->rows(3)
                                            ->placeholder('Ingrese la dirección del cliente'),

                                        Forms\Components\TextInput::make('phone')
                                            ->label('Celular')
                                            ->maxLength(15)
                                            ->tel() // Input con validación para números de teléfono
                                            ->placeholder('Ingrese el número de teléfono'),

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

                    Forms\Components\Wizard\Step::make('Models')
                    ->label('Modelos')
                    ->schema([
                    // Tab 2: Cargar lista de modelos
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
                        ]),
                        Forms\Components\Wizard\Step::make('Items import')
                                ->label('Importar items')
                                ->schema([
                                    // Tab 4: Cargar lista de ítems de la orden
                                    Forms\Components\Toggle::make('order_items_import')
                                    ->default(true)
                                ->dehydrated(false) // No se guarda en la base de datos

                                    ->label('ordenar'),

                                    Forms\Components\TextArea::make('order_items_text')
                                ->label('Pedido Items (Pegue Texto)')
                                ->placeholder("Item\tModelo\tNombre\tNúmero\tOtros\tTalle\tCantidad\tProductos\n1\tModelo 1\tJugador 1\t10\t0rh+\tm-cab\t1\tCamiseta,Short\n2\tModelo 2\tJugador 2\t1\t0rh+\tg-cab\t1\tCamiseta,Short")
                                ->rows(8)
                                ->dehydrated(false) // No se guarda en la base de datos
                                ->helperText('Pegue los elementos del pedido separados por TAB para las columnas y ENTER para las filas.')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    // Procesar el texto y actualizar el estado del Repeater
                                    $items = self::parseOrderItemsText($state, $set, $get);
                                    $set('orderItems', $items); // Actualiza el Repeater
                                }),
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
                                            ->readOnly()
                                            ->numeric()
                                           // ->disabled()
                                            ->default(0)
                                            ->live()
                                            ->required()
                                            ->afterStateUpdated(fn ($state, $set, $get) => self::updateSubtotal($set, $get)),
                                        Forms\Components\TextInput::make('subtotal')
                                            ->label('Subtotal')
                                            ->readOnly()
                                            ->numeric()
                                            // ->disabled() // Deshabilita el campo para que no se pueda editar
                                            ->live()
                                            ->default(0)
                                            ->required(),


                                        Forms\Components\select::make('ProductsItem')
                                            ->label('Productos')
                                            ->searchable()
                                            ->multiple()
                                            ->relationship('product', 'code') // Relación con la tabla products
                                            ->live()
                    //            ->dehydrated(false) // No se guarda en la base de datos

                                        ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getPrice($set, $get))

                                    ])
                                    ->columns(9)
                                    ->live()
                                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getTotal($set, $get))
                                     ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getRefences($set, $get))


                                    ->required(),
                                    Forms\Components\TextInput::make('total')
                                        ->label('Total')
                                        ->readOnly()
                                        ->numeric()
                                        ->live() // Habilita la reactividad
                                        //->disabled() // Deshabilita el campo para que no se pueda editar
                                        ->default(0)
                                        ->numeric()
                                        ->required(),

                                ])
                                ,
                                Forms\Components\Wizard\Step::make('References')
                                ->label('Referencias')

                        ->schema([
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

                                    Forms\Components\Select::make('product_id')
                                        ->label('Producto')
                                        // ->disabled()
                                        //->readOnly()

                                        ->relationship('product', 'code') // Relación con la tabla products
                                        ->default(1)
                                        ->required(),
                                        Forms\Components\Select::make('size_id')
                                        ->label('Talle')
                                        ->default(1)
                                        //->readOnly()

                                        // ->disabled()
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
                                        // ->disabled()
                                        ->default(1)
                                        ->live(onBlur: true)

                                        ->numeric()
                                        ->required(),
                                       
                                ])
                                ->live(onBlur: true)

                                ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getitems($set, $get))
                                
                             //   ->columns(6)
                                ->live() // Habilita la reactividad
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
                            ]),
                            Forms\Components\Wizard\Step::make('Questions')
                            ->label('Informacion adicional')

                            ->schema([   
                                TableRepeater::make('questionAnswers')
                               ->label('Questions')
                               ->relationship('questionAnswers')
                               ->schema([
                                    Forms\Components\Select::make('question_id')
                                    ->relationship('question','text')    
                                    ->label('Cuestionario')
                                    ->live()
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
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('bitrix_id'),
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
                                        ->default(now())
                                        ->required(),
                            Forms\Components\TextInput::make('amount')
                                ->required()
                                ->suffix('Gs.')
                                ->numeric(),
                            Forms\Components\Select::make('payment_method_id')
                                ->relationship('paymentMethod', 'name')
                                ->required(),
                            Forms\Components\TextInput::make('reference')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\FileUpload::make('image')->label('Image')
                            ->directory('pay')
                            ->required(),
                            Forms\Components\TextInput::make('seller_id')
                                ->required()
                                ->numeric(),
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
                                    ->where('order_references.order_id', $record->id)
                                    ->where('order_references.item', $item->item)
                                    ->pluck('categories.name')
                                    ->implode('+ ');
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
            OrderQuestionAnswerRelationManager::class
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
$total = 0;
foreach ($itemsorg as $item) {
    $price = 0;
    
    foreach ($references as $ref) {
        if ($ref['item'] == $item['item']) {
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
            'ProductsItem' => $item['ProductsItem'],
            'price' => $price,
            'subtotal' => $price * $item['quantity'],
        ];
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
        foreach ($item['ProductsItem'] as $product) {

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
protected static function parseOrderItemsText(string $text, $set,$get): array
{
    $odr=$get('order_items_import');
    $references = [];
    $items = [];
    $total = 0;
    $c = 0;
    $if_order = true;

    // Convertir las líneas en arrays asociativos
    $lines = array_map(function ($line) {
        $columns = explode("\t", trim($line));
        return [
            'id' => $columns[0] ?? '',
            'model' => $columns[1] ?? '',
            'name' => $columns[2] ?? '',
            'number' => $columns[3] ?? '',
            'other' => $columns[4] ?? '',
            'size' => $columns[5] ?? '',
            'quantity' => (int) ($columns[6] ?? 0),
            'products' => explode(',', $columns[7] ?? ''), // Convertir productos en array
        ];
    }, explode("\n", trim($text)));

    // Ordenar primero por 'size' y luego por 'products'
    if ($odr) {
        usort($lines, function ($a, $b) {
            return  strcmp(implode(',', $b['products']), implode(',', $a['products']))?: $a['size'] <=> $b['size'];
        });
    }

    // Procesar cada línea después de ordenar
    foreach ($lines as $line) {
        $c++;
        $price = 0;
        $productIds = [];

        // Obtener IDs de productos
        foreach ($line['products'] as $code) {
            $ids = self::getProductIdByName(trim($code));
            if (!empty($ids)) {
                $productIds[] = $ids;
            }
        }

        // Calcular precios
        foreach ($productIds as $productId) {
            $unitPrice = self::getPPrice($productId, self::getSizeIdByName($line['size']));
            $price += $unitPrice;
            $references[] = [
                'item' => $c,
                'product_id' => $productId,
                'size_id' => self::getSizeIdByName($line['size']),
                'quantity' => $line['quantity'],
                'price' => $unitPrice,
                'subtotal' => $line['quantity'] * $unitPrice,
            ];
        }

        // Calcular subtotal y total
        $subtotal = $price * $line['quantity'];
        $total += $subtotal;

        // Agregar datos al array de items
        $items[] = [
            'item' => $c,
            'model' => $line['model'],
            'name' => $line['name'],
            'number' => $line['number'],
            'other' => $line['other'],
            'size_id' => self::getSizeIdByName($line['size']),
            'quantity' => $line['quantity'],
            'ProductsItem' => $productIds,
            'price' => $price,
            'subtotal' => $subtotal,
        ];
    }

    // Asignar los valores finales
    $set('references', $references);
    $set('total', $total);

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
