<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentHistoryResource\Pages;
use App\Filament\Resources\PaymentHistoryResource\RelationManagers;
use App\Models\PaymentHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentHistoryResource extends Resource
{
    protected static ?string $model = PaymentHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static ?string $navigationGroup = 'Pedidos';
    protected static ?string $navigationLabel = 'Historial de pagos';
    protected static ?string $pluralLabel = 'Historial de pagos'; 
    public static function form(Form $form): Form
    {
        return $form
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
                ->directory('orders')
                ->required(),
                
                Forms\Components\TextInput::make('seller_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'id')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->label('fecha')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_id')
                    ->numeric()
                    ->label('orden')

                    ->sortable(),
                Tables\Columns\TextColumn::make('date_confirmation')
                    ->date()
                    ->label('fecha de confirmacion')

                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('PaymentMethod.name')
                    ->numeric()
                    ->label('Metodo de pago')

                    ->sortable(),
                Tables\Columns\TextColumn::make('reference')
                    ->label('Referencia')
                    ->searchable(),
                    Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->numeric()
                    ->suffix(' Gs.')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('status')
                    ,
                Tables\Columns\TextColumn::make('order.seller.name')
                    ->numeric()
                    ->label('Vendedor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_conf_id')
                    ->numeric()
                ->label('Confirmado por')
                    ->sortable()
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentHistories::route('/'),
            'create' => Pages\CreatePaymentHistory::route('/create'),
            'view' => Pages\ViewPaymentHistory::route('/{record}'),
            'edit' => Pages\EditPaymentHistory::route('/{record}/edit'),
        ];
    }
}
