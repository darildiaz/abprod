<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductionPackageResource\Pages;
use App\Filament\Resources\ProductionPackageResource\RelationManagers;
use App\Models\Operator;
use App\Models\ProductionPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\OrderReference;

class ProductionPackageResource extends Resource
{
    protected static ?string $model = ProductionPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = "Production";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\select::make('order_id')
                    ->label('Order')
                    ->relationship('order',
                    'id')
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getOrderProducts($set, $get))

                    ->required(),
                Forms\Components\Select::make('center_id')
                    ->relationship('center', 'name')
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::getOperator($set, $get))

                    ->required(),
                Forms\Components\Select::make('operator_id')
                    //->relationship('operator', 'name')
                    ->required(),
            Forms\Components\Repeater::make('productions')
                    ->live()
                    ->label('Productions')
                    ->schema([
                            Forms\Components\Select::make('product_id')
                            ->relationship('product', 'code')

                    ->label('Product')
                            // ->options(fn (callable $get) => self::getOrderProductOptions($get('order_id')))
                                ->required(),

                            Forms\Components\TextInput::make('quantity')
                                ->label('Quantity')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('price')
                                ->label('Price')
                                ->numeric()
                                ->required(),
                        ])
                ->columns(2)
                ->hidden(fn (callable $get) => !$get('order_id')&&!$get('operator_id')&&!$get('center_id'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
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
            'index' => Pages\ListProductionPackages::route('/'),
            'create' => Pages\CreateProductionPackage::route('/create'),
            'view' => Pages\ViewProductionPackage::route('/{record}'),
            'edit' => Pages\EditProductionPackage::route('/{record}/edit'),
        ];
    }
    public static function getOperator($set, $get){
        $op= Operator::where ('center_id',$get('center_id'));
      //  $set();
    }
    public static function getOrderProducts($set, $get)
    {
       // if(!$get('order_id')) {


        $order_id = $get('order_id');
        $products = OrderReference::where('order_id', $order_id)
        ->selectRaw('product_id, SUM(quantity) AS quantity_t')
        ->groupBy('product_id')
        ->get();

        $prod = [];
        foreach ($products as $product) {
            $prod[] = [
                'product_id' => $product->product_id,
                'quantity' => $product->quantity_t,
             //   'price' => $product->price,
            ];
        }

        $set('productions', $prod);
  //  }
    }
}
