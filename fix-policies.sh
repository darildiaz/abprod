#!/bin/bash

# Función para renombrar archivos si existen
rename_file() {
    local old_name=$1
    local new_name=$2
    if [ -f "$old_name" ]; then
        mv "$old_name" "$new_name"
        echo "Renombrado: $old_name -> $new_name"
    else
        echo "No se encontró: $old_name"
    fi
}

# Función para crear directorio si no existe
ensure_dir() {
    local dir=$1
    if [ ! -d "$dir" ]; then
        mkdir -p "$dir"
        echo "Creado directorio: $dir"
    fi
}

# Ir al directorio de trabajo
cd /var/www/html || exit

# Asegurar que los directorios existan
ensure_dir "/var/www/html/app/Policies"

# Renombrar archivos de políticas
echo "Corrigiendo nombres de archivos de políticas..."
cd /var/www/html/app/Policies || exit

# Renombrar archivos de políticas
rename_file "rollPolicy.php" "RollPolicy.php"
rename_file "materialListPolicy.php" "MaterialListPolicy.php"
rename_file "paymentPolicy.php" "PaymentPolicy.php"
rename_file "materialPolicy.php" "MaterialPolicy.php"
rename_file "prodDiscountPolicy.php" "ProdDiscountPolicy.php"
rename_file "sizeGroupPolicy.php" "SizeGroupPolicy.php"
rename_file "rollProdtPolicy.php" "RollProdtPolicy.php"

# Arreglar problema de EditProfile.php
echo "Solucionando problema de EditProfile.php..."

# Buscar y eliminar todos los archivos EditProfile.php en diferentes ubicaciones
find /var/www/html -name EditProfile.php -exec rm -f {} \;
echo "Eliminados todos los archivos EditProfile.php existentes"

# Asegurar que existe el directorio Auth
ensure_dir "/var/www/html/app/Filament/Pages/Auth"

# Crear archivo en la ubicación correcta con el contenido correcto y namespace modificado
cat > "/var/www/html/app/Filament/Pages/Auth/EditProfile.php" << 'EOL'
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
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
}
EOL
echo "Creado archivo EditProfile.php en la ubicación correcta con contenido simplificado"

# Limpiar cachés de autoloader
cd /var/www/html
composer dump-autoload -o
echo "Recargado autoloader"

# Volver al directorio original
cd /var/www/html
echo "Corrección de archivos completada" 