<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\BelongsToSelect;
use App\Models\QuestionCategory;


class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = "Mantenimiento";
    protected static ?string $pluralLabel = 'Cuentionarios';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Campo de texto para la pregunta
            TextInput::make('text')
            ->label('Question Text') // Etiqueta del campo
            ->required()
            ->placeholder('Enter the question text')
            ->maxLength(255),

        // Selección del tipo de dato
        Select::make('type')
            ->label('Question Type')
            ->required()
            ->options([
                'string' => 'String',
                'integer' => 'Integer',
                'list' => 'List',
            ])
            ->default('string'),

        // Campo de texto para las opciones (solo si el tipo es "list")
        Textarea::make('options')
            ->label('Options (for list type)')
            ->placeholder('Enter options separated by commas')
            ->helperText('This field is only relevant if the type is "list".')
            ->maxLength(65535)
            ->visible(fn (callable $get) => $get('type') === 'list'),

        // Toggle para marcar si la pregunta es obligatoria
        Toggle::make('is_required')
            ->label('Is Required')
            ->default(false),

        // Relación con la categoría de la pregunta
        BelongsToSelect::make('category_id')
            ->label('Category')
            ->relationship('category', 'name') // Relación definida en el modelo
            ->searchable() // Permite buscar entre las categorías
            ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('category.name')->label('Category Name'), 
                Tables\Columns\TextColumn::make('text')->label('Question Text')->searchable(),
                Tables\Columns\BooleanColumn::make('is_required')->label('Is Required'),
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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
