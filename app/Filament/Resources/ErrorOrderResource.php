<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ErrorOrderResource\Pages;
use App\Filament\Resources\ErrorOrderResource\RelationManagers;
use App\Models\ErrorOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ErrorOrderResource extends Resource
{
    protected static ?string $model = ErrorOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = "Produccion";
    protected static ?string $navigationLabel = 'Errores en pedidos';
    
    protected static ?string $pluralLabel = 'Errores en pedidos'; 
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                ->default(now())
                    ->required(),
                    Forms\Components\select::make('order_id')
                    ->label('Pedido')
                    ->relationship('order', 'id',
                        modifyQueryUsing: function (Builder $query): Builder {
                            return $query->where('status', 0);
                        }
                    )
                    ->searchable()
                    ->live()
                    ->required(),

                Forms\Components\Select::make('center_id')
                ->label('Centro con error')
                ->relationship('center', 'name')
                ->required(),
                Forms\Components\Select::make('product_id')
                ->label('Producto')
                ->relationship('product', 'name',
                        modifyQueryUsing: function (Builder $query, $get) {
                            $query->whereHas('orderReferences', function (Builder $query) use ($get) {
                                $query->where('order_id', $get('order_id'));
                            });
                        }
                    )
                ->required(),
                Forms\Components\Select::make('part_id')
                ->relationship('part', 'name')
                ->label('Parte')
                ->required(),
                Forms\Components\TextInput::make('quantity')
                ->label('Cantidad')
                ->required()
                ->numeric(),   
                Forms\Components\TextInput::make('item')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('obs_det')
                ->label('Observaciones detalle')
                    ->required(),

                Forms\Components\Textarea::make('obs_error')
                ->label('Observaciones error')
                    ,
                    
                Forms\Components\Toggle::make('tela')
                    ->required(),
                
                  
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
                
                ->label('centro con error')    
                ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.code')
                ->label('Producto')
                ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('part.name')
                ->label('Parte')    
                ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('obs_det')
                ->label('Observaciones detalle')
                ->searchable(),
                Tables\Columns\TextColumn::make('obs_error')
                ->label('Observaciones error')   
                ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                ->label('Observaciones error')   
                ->searchable(),
                Tables\Columns\IconColumn::make('tela')
                ->label('uso de Tela')    
                ->boolean(),
                Tables\Columns\TextColumn::make('quantity')
                ->label('Cantidad')    
                ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('part.loss_percentage')
                    ->label('Porcentaje de perdida')
                    ->sortable()

                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

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
            RelationManagers\ReorderRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListErrorOrders::route('/'),
            'create' => Pages\CreateErrorOrder::route('/create'),
            'view' => Pages\ViewErrorOrder::route('/{record}'),
            'edit' => Pages\EditErrorOrder::route('/{record}/edit'),
        ];
    }
}
