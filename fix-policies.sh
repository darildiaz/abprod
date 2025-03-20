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

# Verificar si existe el directorio Filament
if [ -d "/var/www/html/app/Filament" ]; then
    echo "Corrigiendo problema de EditProfile.php..."
    
    # Verificar si existe EditProfile.php en Pages
    if [ -f "/var/www/html/app/Filament/Pages/EditProfile.php" ]; then
        # Verificar el namespace en el archivo
        if grep -q "namespace App\\Filament\\Pages\\Auth;" "/var/www/html/app/Filament/Pages/EditProfile.php"; then
            # Si el archivo tiene namespace Auth pero está en la ubicación incorrecta
            ensure_dir "/var/www/html/app/Filament/Pages/Auth"
            # Mover el archivo a Auth/ y renombrarlo
            mv "/var/www/html/app/Filament/Pages/EditProfile.php" "/var/www/html/app/Filament/Pages/Auth/EditProfile.php"
            echo "Movido EditProfile.php al directorio Auth/ para corregir namespace"
        else
            # Editar el namespace para que coincida con la ubicación
            sed -i 's/namespace App\\Filament\\Pages\\Auth;/namespace App\\Filament\\Pages;/g' "/var/www/html/app/Filament/Pages/EditProfile.php"
            echo "Corregido namespace en EditProfile.php"
        fi
    fi
    
    # Verificar duplicado en Auth/
    if [ -f "/var/www/html/app/Filament/Pages/Auth/EditProfile.php" ] && [ -f "/var/www/html/app/Filament/Pages/EditProfile.php" ]; then
        # Si existen ambos archivos, eliminar uno para evitar conflicto
        rm "/var/www/html/app/Filament/Pages/EditProfile.php"
        echo "Eliminado archivo duplicado para evitar conflicto de clase"
    fi
else
    echo "El directorio Filament no existe"
fi

# Volver al directorio original
cd /var/www/html
echo "Corrección de archivos completada" 