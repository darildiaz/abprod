<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static ?string $navigationIcon = 'heroicon-o-user-circle';
    Protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $navigationGroup = "Mantenimiento";
    protected static ?string $pluralLabel = 'Usuarios';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('email')
                ->label('Correo Electrónico')
                ->email()
                ->required()
                ->maxLength(255),
            // Forms\Components\DateTimePicker::make('email_verified_at')
            //     ->label('Verificación de correo'),
            Forms\Components\TextInput::make('password')
                ->label('Contraseña')
                ->hiddenOn('edit')
                ->password()
                ->required()
                ->maxLength(255),
       
            Forms\Components\CheckboxList::make('roles')
                  ->relationship('roles', 'name')
                  ->searchable(),
            ]);
        }

        public static function table(Table $table): Table
        {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('Nombre')
                ->searchable(),
            Tables\Columns\TextColumn::make('email')
                ->label('Correo Electrónico')
                ->searchable(),
            Tables\Columns\TextColumn::make('email_verified_at')
                ->label('Verificado en')
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('roles.name')
                ->label('rol')
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Creado en')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->label('Actualizado en')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
            //
            ])
            
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('changePassword')
                    ->label('Cambiar Contraseña')
                    ->icon('heroicon-o-key')
                    ->form([
                        Forms\Components\TextInput::make('password')
                            ->label('Nueva Contraseña')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->confirmed(),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirmar Contraseña')
                            ->password()
                            ->required(),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->update([
                            'password' => bcrypt($data['password']),
                        ]);
                        
                        Notification::make()
                            ->title('Contraseña actualizada')
                            ->success()
                            ->send();
                    }),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
