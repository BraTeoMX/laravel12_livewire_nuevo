<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración Regional de México
    |--------------------------------------------------------------------------
    |
    | Esta configuración define los formatos y parámetros específicos
    | para la regionalización de México.
    |
    */

    // Formato de fecha (d/m/Y = 31/12/2023)
    'date_format' => 'd/m/Y',
    
    // Formato de fecha y hora (d/m/Y H:i)
    'datetime_format' => 'd/m/Y H:i',
    
    // Formato de hora (24h)
    'time_format' => 'H:i',
    
    // Separador de decimales (punto para miles, coma para decimales)
    // En México se usa: 1,000.50 (coma para miles, punto para decimales)
    // pero en programación el estándar internacional es al revés
    'decimal_separator' => '.',
    'thousand_separator' => ',',
    
    // Configuración de moneda (Pesos Mexicanos)
    'currency' => [
        'symbol' => '$',
        'code' => 'MXN',
        'name' => 'Peso Mexicano',
        'position' => 'before', // Símbolo antes del número: $100.00
        'decimals' => 2,
        'thousand_separator' => ',',
        'decimal_separator' => '.',
    ],
    
    // Zona horaria por defecto
    'timezone' => 'America/Mexico_City',
    
    // Configuración de números
    'number_decimals' => 2,
    
    // Primera letra del día en mayúscula (estilo mexicano)
    'capitalize_first_letter' => true,
];
