#!/bin/bash

# Renombrar archivos de políticas para cumplir con PSR-4
cd /var/www/html/app/Policies

# Renombrar archivos de políticas
mv rollPolicy.php RollPolicy.php
mv materialListPolicy.php MaterialListPolicy.php
mv paymentPolicy.php PaymentPolicy.php
mv materialPolicy.php MaterialPolicy.php
mv prodDiscountPolicy.php ProdDiscountPolicy.php
mv sizeGroupPolicy.php SizeGroupPolicy.php
mv rollProdtPolicy.php RollProdtPolicy.php

# Renombrar archivo de perfil
cd /var/www/html/app/Filament/Pages/Auth
mv EditProfile.php EditProfile.php 