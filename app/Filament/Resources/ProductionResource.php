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
use App\Models\OrderReference;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Support\Facades\Log;
class ProductionResource extends Resource
implements HasShieldPermissions
{
    protected static ?string $model = Production::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static ?string $navigationGroup = 'Produccion';
    public static ?string $navigationLabel = 'Produccion';
    protected static ?string $pluralLabel = 'Producciones';
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
            'center_all',
            'ver_todos'
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('date')
                    ->default(now()),

                Forms\Components\select::make('order_id')
                    ->label('Pedido')
                    ->relationship('order', 'id',
                        modifyQueryUsing: function (Builder $query): Builder {
                            return $query->where('status', 1);
                        }
                    )
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getOrderProducts($set, $get))
                    ->required(),

                Forms\Components\Select::make('center_id')
                    ->label('Centro')
                    ->relationship('center', 'name')
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getOrderProducts($set, $get))
                    ->required(),

                Forms\Components\Select::make('operator_id')
                    ->label('Operador')
                    ->options(function (callable $get) {
                        return \App\Models\Operator::query()
                            ->where('center_id', $get('center_id'))
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->reactive()
                    ->required(),
                    Forms\Components\Toggle::make('status')
                    ->label('completado')

                    ->required(),
                Forms\Components\Repeater::make('details')
                    ->relationship('details')
                    ->label('detalles')
                    ->live()
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Producto')
                            ->relationship('product', 'code')

                            ->required(),
                        Forms\Components\TextInput::make('prodquantity')
                            //->readOnly()
                            ->label('Cantidad Producida')

                            ->live() // Para actualizar en tiempo real    
                            ->disabled(),
                        Forms\Components\TextInput::make('orderquantity')
                            //->readOnly()
                            ->label('Cantidad Pedido')
                            ->live() // Para actualizar en tiempo real    
                            ->disabled(),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(fn (callable $get) => $get('orderquantity') - $get('prodquantity'))
                            ->required(),
                        
                        Forms\Components\hidden::make('price')
                            ->label('Precio')
                           // ->numeric()
                           ->default(0)
                            ->required(),

                        Forms\Components\TextInput::make('valid_amount')
                            ->label('Cantidad Valida')
                           // ->numeric()
                           ->default(0)
                            ->required(),
                    ])
                    ->columns(3)
                    ->columnSpanFull()
                    ->hidden(fn (callable $get) => !$get('order_id') && !$get('operator_id') && !$get('center_id') )
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(fn () => auth()->user()->can('ver_todos_production')
        ? Production::query() // Si es admin, muestra todos los pedidos
        : Production::query()->where('operator_id', auth()->id()) // Si no, filtra por manager_id
        )
        ->defaultGroup('status')

        ->groups([ 
            Group::make('status')
            ->label('estado')
            
            ->collapsible(),
                    ])
    ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_date')
                    ->label('Fecha entrega')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_id')
                    ->label('Pedido')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('center.name')
                    ->label('Centro')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('operator.name')
                    ->label('Operador')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('details.product.name')
                    ->label('productos')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            0 => 'Pendiente',
                            1 => 'Completado',
                            default => 'Desconocido',
                        };
                    })->color(fn (string $state): string => match ($state) {
                        '0' => 'gray',
                        '1' => 'success',
                        '2' => 'warning',
                        default => 'secondary',
                        })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('center_id')
                    ->label('Centro')
                    ->relationship('center', 'name'),
            
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('changeStatus')
                ->visible(fn ($record) => $record->status !== 2)
                ->visible(fn () => auth()->user()->can('status_production_order'))
                ->label('editar estado')
                ->action(function ($record, $data) {
                    $record->status = $data['status'];
                    // Actualizar fechas según el estado seleccionado
                    // if ($data['status'] == 1) {
                    //     $record->completion_date = now();
                    // } else
                    if ($data['status'] == 1) {
                         $record->completed_date = now();
                    }
                    $record->save();
                })
                ->form([
                    Forms\Components\Select::make('status')
                        ->options([
                           
                            1 => 'Completado',
                        ])
                        ->default(1)
                        ->required(),
                        ])->requiresConfirmation(),
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('Cargar_rollo')
            ->label('Ver Detalle de Referencias')
          //  ->action()
            ->form([
                Forms\Components\Select::make('roll_id')
                    ->label('rollo')
                    ->relationship('roll', 'id')
                    ->createOptionForm([
                        Forms\Components\DatePicker::make('date')
                ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('impresora')
                    ->required()
                    ->maxLength(255),

                    ])
                    ->required(),
                
            ])
            ->requiresConfirmation(),
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
            'view' => Pages\ViewProduction::route('/{record}'),
            'edit' => Pages\EditProduction::route('/{record}/edit'),
        ];
    }
    public static function getOrderProducts($set, $get)
    {
       // if(!$get('order_id')) {


        $order_id = $get('order_id');
        $centerId = $get('center_id');
        if (!$order_id || !$centerId) {
            return;
        }
        $products = OrderReference::where('order_references.order_id', $order_id)
    ->join('order_items', function ($join) {
        $join->on('order_references.order_id', '=', 'order_items.order_id')
             ->on('order_references.item', '=', 'order_items.item');
    })
    ->selectRaw('order_references.product_id, 
                 SUM(order_references.quantity) AS quantity_t, 
                 COUNT(DISTINCT CONCAT(order_items.model, "-", order_references.size_id)) AS model_size_count, 
                 COUNT(DISTINCT order_items.model) AS model_count')
    ->groupBy('order_references.product_id')
    ->get();

            $producedQuantities = Production::join('productiondets as pd', 'pd.production_id', '=', 'productions.id')
            ->where('productions.order_id', $order_id)
            ->where('productions.center_id', $centerId)
            ->selectRaw('pd.product_id, SUM(pd.quantity) AS sumprod')
            ->groupBy('pd.product_id')
            ->get()
            ->keyBy('product_id');
        Log::info('Produced Quantities:', $producedQuantities->toArray());

        $prod = [];
        $amount=0;
        foreach ($products as $product) {
                if ($product->product->is_producible) {
                    $prodquantity = isset($producedQuantities[$product->product_id]) 
                    ? $producedQuantities[$product->product_id]->sumprod 
                    : 0;
                    switch (self::getProducttype($product->product_id, $centerId)) {
                        case  1:
                            $amount = $product->quantity_t;
                            break;
                        case  2:
                                $amount = $product->model_size_count;
                                break;
                        case  3:
                                    $amount = $product->model_count;
                                    break;
                    }
                    $prod[] = [
                    'product_id' => $product->product_id,
                    'prodquantity' => $prodquantity,
                    'orderquantity' => $product->quantity_t,
                    'quantity' => $product->quantity_t-$prodquantity,
                    'valid_amount' => $amount,
                    'price' => self::getProductPrice($product->product_id, $centerId),
                ];
                $amount=0;
                    $maxQuantities[$product->product_id] = $product->quantity_t;
                }
                
        }

        $set('details', $prod);
    }
    public static function getProductPrice(int $productId, int $centerId): ?int
    {
        return \App\Models\ProductCenter::where('product_id', $productId)
            ->where('center_id', $centerId)
            ->value('price') ?? 0; // Retrieves the price directly, returns 0 if null
    }
    public static function getProducttype(int $productId, int $centerId): ?int
    {
        return \App\Models\ProductCenter::where('product_id', $productId)
            ->where('center_id', $centerId)
            ->value('type_of_valuation') ?? 1; // Retrieves the price directly, returns 0 if null
    }
}
