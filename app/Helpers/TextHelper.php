<?php

namespace App\Helpers;

class TextHelper
{
    public static function getFirstLines($html, $lines = 2)
    {
        // Limpiar el HTML y obtener texto plano
        $text = strip_tags($html);
        
        // Dividir en líneas
        $textLines = explode("\n", $text);
        
        // Filtrar líneas vacías
        $textLines = array_filter($textLines, function($line) {
            return trim($line) !== '';
        });
        
        // Tomar las primeras líneas
        $firstLines = array_slice($textLines, 0, $lines);
        
        // Unir las líneas con saltos de línea
        return implode("\n", $firstLines);
    }
} 