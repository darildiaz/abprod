<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
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
   
    public static ?string $navigationGroup = 'Pedidos';


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

            Forms\Components\Textarea::make('address')
                ->label('Dirección')
                ->rows(3)
                ->placeholder('Ingrese la dirección del cliente'),

            Forms\Components\TextInput::make('phone')
                ->label('Teléfono')
                ->maxLength(15)
                ->tel() // Input con validación para números de teléfono
                ->placeholder('Ingrese el número de teléfono'),

            Forms\Components\BelongsToSelect::make('user_id')
                ->label('Vendedor')
                ->relationship('user', 'name') // Relación con el modelo User
                ->default(auth()->id()) // Predetermina el usuario logueado
                ->required()
                
                ->searchable()
                ->disabled() // Hace que el campo no sea editable
                ->placeholder('Usuario autenticado'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                //
            ])
            ->actions([
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
