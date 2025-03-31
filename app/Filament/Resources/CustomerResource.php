<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\Resources\CustomerResource\RelationManagers\OrderRelationManager;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group'; // Icono del menú
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $pluralLabel = 'Clientes'; // Etiqueta plural

    public static ?string $navigationGroup = 'Pedidos';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            Forms\Components\TextInput::make('nif')
                ->label('NIF')
                ->required()
                ->maxLength(20)
                ->unique() // El NIF debe ser único
                ->placeholder('Ingrese el NIF del cliente'),

            Forms\Components\TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->placeholder('Ingrese el nombre del cliente'),
            Forms\Components\TextInput::make('email')
            ->email()
                ->label('Correo electrónico'),
            Forms\Components\Textarea::make('address')
                ->label('Dirección')
                ->required()
                ->rows(3)
                ->placeholder('Ingrese la dirección del cliente'),

            Forms\Components\TextInput::make('phone')
                ->label('Teléfono')
                ->maxLength(15)
                ->tel() // Input con validación para números de teléfono
                ->placeholder('Ingrese el número de teléfono'),

            Forms\Components\Select::make('user_id')
                ->label('Vendedor')
                ->relationship('user', 'name') // Relación con el modelo User
                ->default(auth()->id()) // Predetermina el usuario logueado
                ->required()
                ->hiddenOn('edit')

                ->searchable()
                //->disabled() // Hace que el campo no sea editable
                ->placeholder('Usuario autenticado'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(fn () => auth()->user()->can('ver_todos_order')
            ? Customer::query() // Si es admin, muestra todos los pedidos
            : Customer::query()->where('user_id', auth()->id()) // Si no, filtra por manager_id
            )
            ->columns([

                Tables\Columns\TextColumn::make('nif')
                    ->label('NIF')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Vendedor') // Mostrar el nombre del vendedor
                    ->sortable(),
                Tables\Columns\TextColumn::make('orders_count')
                    ->label('cant. pedidos')
                    ->counts('orders') // Cuenta las órdenes del cliente
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user.name')
                ->label('Vendedor') // Mostrar el nombre del vendedor

                ->relationship('user', 'name')
                 ->default(auth()->id()),


            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('ver_pedido')
                ->label("ver pedido")
                ->url(fn ($record) => route('filament.admin.resources.orders.index', [
                    'tableFilters[customer][name][value]' => $record->id
                ]))
                ->openUrlInNewTab(), // Opcional: abrir en una nueva pestaña
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
            OrderRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
