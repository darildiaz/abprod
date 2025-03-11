<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductiondetResource\Pages;
use App\Filament\Resources\ProductiondetResource\RelationManagers;
use App\Models\Productiondet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Grouping\Group;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

use Filament\Tables\Columns\Summarizers\Sum;

class ProductiondetResource extends Resource
{
    protected static ?string $model = Productiondet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static ?string $navigationGroup = 'Produccion';
    protected static ?string $pluralLabel = 'Detalles de Produccion';
    public static ?string $navigationLabel = 'Produccion Detalles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\DatePicker::make('date')
                ->label('Fecha'),
            Forms\Components\TextInput::make('production_id')
                ->label('ID de Producción')
                ->required()
                ->numeric(),
            Forms\Components\TextInput::make('product_id')
                ->label('ID de Producto')
                ->required()
                ->numeric(),
            Forms\Components\TextInput::make('quantity')
                ->label('Cantidad')
                ->required()
                ->numeric(),
            
            Forms\Components\TextInput::make('price')
                ->label('Precio')
                ->required()
                ->numeric()
                ->prefix('Gs.'),
            Forms\Components\Toggle::make('pay')
                ->label('Pago')
                ->required(),
            ]);
        }

        public static function table(Table $table): Table
        {
        return $table
                ->defaultGroup('production.center.name')
                ->groups([ 
                    Group::make('production.center.name')
                    ->label('centro de produccion')
                    ->collapsible(),
                    Group::make('production.operator.name')
                    ->label('Operador')
                    ->collapsible(),
                ])
            ->columns([
            Tables\Columns\TextColumn::make('production.date')
                ->label('Fecha de Producción')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('production.order_id')
                ->label('Id pedido')
                ->sortable(),
            Tables\Columns\TextColumn::make('production.center.name')
                ->label('Centro de Producción')
                ->numeric()
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('production.operator.name')
                ->label('Operador')
                ->numeric()
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('product.code')
                ->label('Código del Producto')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('production.status')
                ->label('Estado de Producción')
                
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('quantity')
                ->label('Cantidad')
                ->summarize(Sum::make())

                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('valid_amount')
                ->label('Cantidad valida')
                ->summarize(Sum::make())
                ->numeric()
                ->sortable(),
                
            Tables\Columns\TextColumn::make('price')
                ->label('Precio')
                ->money('Gs.')
                ->sortable(),
            Tables\Columns\TextColumn::make('subtotal')
                ->label('Subtotal')
                //->summarize(Sum::make())

                ->state(function (Productiondet $record): float {
                    return $record->valid_amount * $record->price;
                })
                ->money('Gs.')
                ->sortable(),
            Tables\Columns\IconColumn::make('pay')
                ->label('Pago Realizado')
                ->boolean(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Creado el')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->label('Actualizado el')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tables\Filters\SelectFilter::make('productions.status')
                //     ->label('Estado')
                //     ->options([
                //         '0' => 'Pendiente',
                //         '1' => 'Completado',
                //     ])
                //     ->default('0'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('pay')
                ->visible(fn ($record) => $record->pay !== 2)
                ->visible(fn () => auth()->user()->can('status_production_order'))
                ->label(' estado ')
                ->action(function ($record, $data) {
                    $record->pay = $data['pay'];
                    // Actualizar fechas según el estado seleccionado
                    // if ($data['status'] == 1) {
                    //     $record->completion_date = now();
                    // } elseif ($data['status'] == 2) {
                    //     $record->shipping_date = now();
                    // }
                    $record->save();
                })
                ->form([
                    Forms\Components\Select::make('pay')
                        ->options([
                            0 => 'pendiente',
                            1 => 'pagado',
                        ])
                        ->required(),
                        ])->requiresConfirmation(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),   

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
            'index' => Pages\ListProductiondets::route('/'),
            'create' => Pages\CreateProductiondet::route('/create'),
            'view' => Pages\ViewProductiondet::route('/{record}'),
            'edit' => Pages\EditProductiondet::route('/{record}/edit'),
        ];
    }
}
