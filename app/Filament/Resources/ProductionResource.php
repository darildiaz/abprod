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
class ProductionResource extends Resource
{
    protected static ?string $model = Production::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static ?string $navigationGroup = 'Produccion';
    public static ?string $navigationLabel = 'Produccion';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\hidden::make('date')
                    ->default(now()),

                Forms\Components\select::make('order_id')
                    ->label('Pedido')
                    ->relationship('order', 'id',
                        modifyQueryUsing: function (Builder $query): Builder {
                            return $query->where('status', 0);
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

                Forms\Components\Repeater::make('details')
                    ->relationship('details')
                    ->label('detalles')
                    ->live()
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Producto')
                            ->relationship('product', 'code')
                           // ->disabled()
                            ->required(),


                        Forms\Components\TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->required(),

                        Forms\Components\hidden::make('price')
                            ->label('Precio')
                           // ->numeric()
                            ->required(),
                    ])
                    ->columns(3)
                    ->columnSpanFull()
                    ->hidden(fn (callable $get) => !$get('order_id') && !$get('operator_id') && !$get('center_id'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Fecha')
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
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->numeric()
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
        $products = OrderReference::where('order_id', $order_id)
        ->selectRaw('product_id, SUM(quantity) AS quantity_t')
        ->groupBy('product_id')
        ->get();

        $prod = [];
        foreach ($products as $product) {
            $prod[] = [
                'product_id' => $product->product_id,
                'quantity' => $product->quantity_t,
                'price' => self::getProductPrice($product->product_id, $centerId),
            ];
        }

        $set('details', $prod);
  //  }
    }
    public static function getProductPrice(int $productId, int $centerId): ?int
    {
        return \App\Models\ProductCenter::where('product_id', $productId)
            ->where('center_id', $centerId)
            ->value('price'); // Retrieves the price directly
    }
}
