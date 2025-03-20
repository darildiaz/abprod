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

# Asegurar que los directorios existan
ensure_dir "/var/www/html/app/Policies"
ensure_dir "/var/www/html/app/Filament/Pages/Auth"

# Renombrar archivos de políticas
cd /var/www/html/app/Policies

# Renombrar archivos de políticas
rename_file "rollPolicy.php" "RollPolicy.php"
rename_file "materialListPolicy.php" "MaterialListPolicy.php"
rename_file "paymentPolicy.php" "PaymentPolicy.php"
rename_file "materialPolicy.php" "MaterialPolicy.php"
rename_file "prodDiscountPolicy.php" "ProdDiscountPolicy.php"
rename_file "sizeGroupPolicy.php" "SizeGroupPolicy.php"
rename_file "rollProdtPolicy.php" "RollProdtPolicy.php"

# Renombrar archivo de perfil
cd /var/www/html/app/Filament/Pages/Auth
rename_file "EditProfile.php" "EditProfile.php"

# Volver al directorio original
cd /var/www/html 