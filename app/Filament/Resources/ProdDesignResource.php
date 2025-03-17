<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdDesignResource\Pages;
use App\Filament\Resources\ProdDesignResource\RelationManagers;
use App\Models\ProdDesign;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\Summarizers;


class ProdDesignResource extends Resource
{
    protected static ?string $model = ProdDesign::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationGroup = "Pedidos";
    protected static ?string $navigationLabel = 'Diseños';
    protected static ?string $pluralLabel = 'Diseños';

    public static function form(Form $form): Form
    {
        return $form
        
            ->schema([
                Forms\Components\DatePicker::make('date')
                ->default(now())
                ->label('Fecha')
                    ->required(),
                Forms\Components\TextInput::make('bitrix')
                ->label('Referencia')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                ->label('Tipo')
                ->options(
                    ['Nuevo'=>'Nuevo','Modificacion'=>'Modificacion']
                )
                ->default('Nuevo')    
                ->required(),
                Forms\Components\Select::make('user_id')
                ->label('Diseñador')
                ->relationship('user', 'name')
                ->default(auth()->id())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->groups([ 
            Group::make('type')
            ->label('Tipo')
            ->collapsible(),
            Group::make('user.name')
            ->label('Diseñadores')
            ->collapsible(),
            
            Group::make('date') // Doit être une chaîne, pas une Closure
            ->label('Mes de Emisión')
            ->getTitleFromRecordUsing(fn ($record) => Carbon::parse($record->issue_date)->translatedFormat('F Y')) 
            ->collapsible(),
        ])
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->label('Fecha')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bitrix')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                ->label('Tipo')
                ->summarize(Summarizers\Count::make())
                    ->searchable(),
                Tables\Columns\TextColumn::make('User.name')
                    ->label('Diseñador')
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
            'index' => Pages\ListProdDesigns::route('/'),
            'create' => Pages\CreateProdDesign::route('/create'),
            'view' => Pages\ViewProdDesign::route('/{record}'),
            'edit' => Pages\EditProdDesign::route('/{record}/edit'),
        ];
    }
}
