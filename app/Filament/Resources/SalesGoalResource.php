<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesGoalResource\Pages;
use App\Filament\Resources\SalesGoalResource\RelationManagers;
use App\Models\SalesGoal;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;

class SalesGoalResource extends Resource
{
    protected static ?string $model = SalesGoal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Metas de Ventas';

    protected static ?string $navigationGroup = "ventas";
    protected static ?int $navigationSort = 5;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('team_id')
                ->label('Equipo')
                    ->required()
                    ->relationship('team', 'name')
                    ->live()
                    ,
                Forms\Components\Select::make('user_id')
                ->label('Vendedor')   
                ->reactive()

                ->required()
                    ->options(function (callable $get) {
                        return \App\Models\TeamMember::query()
                            ->where('team_id', $get('team_id'))
                            ->with('user')
                            ->get()
                            ->pluck('user.name', 'user.id')
                            ->toArray();
                    }),
                Forms\Components\Select::make('month')
                ->label('Mes')    
                ->options([
                    '1' => 'Enero',
                    '2' => 'Febrero',
                    '3' => 'Marzo',
                    '4' => 'Abril',
                    '5' => 'Mayo',
                    '6' => 'Junio',
                    '7' => 'Julio',
                    '8' => 'Agosto',
                    '9' => 'Septiembre',
                    '10' => 'Octubre',
                    '11' => 'Noviembre',
                    '12' => 'Diciembre',
                ])
                ->default(now()->format('m'))

                ->required(),
                Forms\Components\TextInput::make('year')
                ->label('Año')    
                ->default(now()->format('Y'))

                ->required()
                    ->numeric(),
                
                TableRepeater::make('goaldDet')
                ->relationship('goaldDet')
                ->schema([
                    Forms\Components\Select::make('category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name'),
                    Forms\Components\TextInput::make('quantity')
                    ->label('Cantidad')
                    ->default(1)
                    ->required()
                    ->live(onBlur: true)
                    // ->afterStateUpdated(function (callable $set, callable $get) {
                    //     $set('amount', $get('quantity') * $get('price'));
                    // })
                    ->numeric(),
                    Forms\Components\TextInput::make('price')
                    ->label('Precio')
                    ->default(1)
                    ->suffix('Gs.')
                    ->live(onBlur: true)
                    // ->afterStateUpdated(function (callable $set, callable $get) {
                    //     $set('amount', $get('quantity') * $get('price'));
                    // })
                    ->required()
                    ->numeric(),
                    Forms\Components\TextInput::make('amount')
                    ->label('Monto')
                    ->readOnly()
                    ->live(onBlur: true)
                    ->suffix('Gs.')
                    ->required()
                    ->numeric(),
                ])
                ->columns(4)
                ->live(onBlur: true)
                ->afterStateUpdated(function (callable $set, callable $get) {
                    self::getTotal($set, $get);
                })
                ->columnSpanFull(),
                Forms\Components\TextInput::make('amount')
                    ->label('Monto')    
                    ->default(0)
                    ->suffix('Gs.')
                    ->live(onBlur: true)
                    ->readOnly()
                    ->required()
                    ->numeric(),
            ]);
    }
public static function getTotal(callable $set, callable $get){
    $total = 0;
    $det=[];
    foreach ($get('goaldDet') as $item) {
        $total += $item['amount'];
        $det[]=[
            'category_id'=>$item['category_id'],
            'quantity'=>$item['quantity'],
            'price'=>$item['price'],
            'amount'=>$item['quantity']*$item['price'],
        ];
        
        
    }
    $set('goaldDet', $det);
     $set('amount', $total);
}
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team.name')
                ->label('Equipo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                ->label('Vendedor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('month')
                ->label('Mes')
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    '1' => 'Enero',
                        '2' => 'Febrero',
                        '3' => 'Marzo',
                        '4' => 'Abril',
                        '5' => 'Mayo',
                        '6' => 'Junio',
                        '7' => 'Julio',
                        '8' => 'Agosto',
                        '9' => 'Septiembre',
                        '10' => 'Octubre',
                        '11' => 'Noviembre',
                        '12' => 'Diciembre',
                    default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                ->label('Año')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                ->label('Monto')
                    ->suffix('Gs.')
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
                Tables\Filters\SelectFilter::make('team_id')
                ->label('Equipo')
                ->relationship('team', 'name'),
                Tables\Filters\SelectFilter::make('user_id')
                ->label('Vendedor')
                ->relationship('user', 'name'),
                Tables\Filters\SelectFilter::make('month')
                ->label('Mes')
                ->options([
                    '1' => 'Enero',
                    '2' => 'Febrero',
                    '3' => 'Marzo',
                    '4' => 'Abril',
                    '5' => 'Mayo',
                    '6' => 'Junio',
                    '7' => 'Julio',
                    '8' => 'Agosto',
                    '9' => 'Septiembre',
                    '10' => 'Octubre',
                    '11' => 'Noviembre',
                    '12' => 'Diciembre',
                ]),
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
            'index' => Pages\ListSalesGoals::route('/'),
            'create' => Pages\CreateSalesGoal::route('/create'),
            'view' => Pages\ViewSalesGoal::route('/{record}'),
            'edit' => Pages\EditSalesGoal::route('/{record}/edit'),
        ];
    }
}
