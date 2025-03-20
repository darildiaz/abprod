<?php
 
namespace App\Filament\Pages\Auth;
 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
 
class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
<<<<<<< HEAD
                TextInput::make('username')
                    ->required()
                    ->maxLength(255),
=======
>>>>>>> 262696ff2e92ccb06359e127e58d36b9f5c35d37
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> 262696ff2e92ccb06359e127e58d36b9f5c35d37
