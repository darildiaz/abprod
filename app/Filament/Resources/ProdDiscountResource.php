<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdDiscountResource\Pages;
use App\Filament\Resources\ProdDiscountResource\RelationManagers;
use App\Models\ProdDiscount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProdDiscountResource extends Resource
{
    protected static ?string $model = ProdDiscount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label('Fecha')
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('operador')    
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->label('Descripción')
                    ->maxLength(255),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->live(onBlur: true)
                    ->default(1)
                    ->afterStateUpdated(fn (callable $set, callable $get) => 
                    $set('subtotal', $get('price')* $get('quantity') ))
                    ->label('Cantidad')
                    ->numeric(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->label('Precio')
                    ->live()
                    ->default(0)
                    ->afterStateUpdated(fn (callable $set, callable $get) => 
                        $set('subtotal', $get('price')* $get('quantity')
                    )) // Busca el `team_id` del vendedor seleccionado
                    ->numeric()
                    ->live(onBlur: true)

                    ->suffix(' Gs.'),
                Forms\Components\TextInput::make('subtotal')
                   ->readOnly(false)
                    ->label('Subtotal')
                    ->numeric(),
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
                Tables\Columns\TextColumn::make('user.name')
                ->label('Operador')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                ->label('Descripción')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                ->label('Cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                ->label('Precio')
                    ->money(' Gs.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
                ->label('Subtotal')
                    ->numeric()
                    ->money(' Gs.')

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
            'index' => Pages\ListProdDiscounts::route('/'),
            'create' => Pages\CreateProdDiscount::route('/create'),
            'view' => Pages\ViewProdDiscount::route('/{record}'),
            'edit' => Pages\EditProdDiscount::route('/{record}/edit'),
        ];
    }
}
