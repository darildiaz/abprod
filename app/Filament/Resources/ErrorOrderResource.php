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

                Forms\Components\Select::make('center_id')->relationship('center', 'name')
                ->required(),
                Forms\Components\Select::make('product_id')->relationship('product', 'name')
                ->required(),
                Forms\Components\Select::make('part_id')->relationship('part', 'name')
                ->required(),
                Forms\Components\TextInput::make('quantity')
                ->required()
                ->numeric(),   
                Forms\Components\TextInput::make('item')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('obs_det'),
                Forms\Components\Textarea::make('obs_error'),
                    
                Forms\Components\Toggle::make('tela')
                    ->required(),
                
                  
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('center.name')
                ->label('centro con error')    
                ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.code')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('part.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('part.loss_percentage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('item')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('obs_det')
                    ->searchable(),
                Tables\Columns\TextColumn::make('obs_error')
                    ->searchable(),
                Tables\Columns\IconColumn::make('tela')
                    ->boolean(),
                Tables\Columns\TextColumn::make('quantity')
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
