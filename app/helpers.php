<?php

// Helper functions for Mexico localization

if (!function_exists('mx_format_date')) {
    /**
     * Formatea una fecha en formato mexicano (d/m/Y)
     *
     * @param mixed $date Fecha a formatear (string, Carbon, DateTime)
     * @param string $format Formato personalizado (opcional)
     * @return string
     */
    function mx_format_date($date, string $format = null): string
    {
        if (!$date) {
            return '';
        }

        $carbon = \Carbon\Carbon::parse($date);
        
        return $format 
            ? $carbon->format($format)
            : $carbon->format('d/m/Y');
    }
}

if (!function_exists('mx_format_datetime')) {
    /**
     * Formatea fecha y hora en formato mexicano (d/m/Y H:i)
     *
     * @param mixed $datetime Fecha y hora a formatear
     * @param string $format Formato personalizado (opcional)
     * @return string
     */
    function mx_format_datetime($datetime, string $format = null): string
    {
        if (!$datetime) {
            return '';
        }

        $carbon = \Carbon\Carbon::parse($datetime);
        
        return $format 
            ? $carbon->format($format)
            : $carbon->format('d/m/Y H:i');
    }
}

if (!function_exists('mx_format_currency')) {
    /**
     * Formatea un número como moneda mexicana (Pesos)
     *
     * @param float|int $amount Cantidad a formatear
     * @param bool $symbol Mostrar símbolo $ (default true)
     * @param int $decimals Número de decimales (default 2)
     * @return string
     *
     * Ejemplos:
     *   mx_format_currency(1234.56) => "$1,234.56"
     *   mx_format_currency(1234.56, false) => "1,234.56"
     */
    function mx_format_currency($amount, bool $symbol = true, int $decimals = 2): string
    {
        $formatted = number_format(
            (float) $amount,
            $decimals,
            '.',
            ','
        );

        return $symbol ? '$' . $formatted : $formatted;
    }
}

if (!function_exists('mx_format_number')) {
    /**
     * Formatea un número con separador de miles mexicano
     *
     * @param float|int $number Número a formatear
     * @param int $decimals Número de decimales (default 2)
     * @return string
     *
     * Ejemplo: mx_format_number(1234567.89) => "1,234,567.89"
     */
    function mx_format_number($number, int $decimals = 2): string
    {
        return number_format(
            (float) $number,
            $decimals,
            '.',
            ','
        );
    }
}

if (!function_exists('mx_format_percentage')) {
    /**
     * Formatea un decimal como porcentaje
     *
     * @param float $decimal Valor decimal (0.15 = 15%)
     * @param int $decimals Decimales a mostrar
     * @return string
     */
    function mx_format_percentage(float $decimal, int $decimals = 2): string
    {
        return number_format(
            $decimal * 100,
            $decimals,
            '.',
            ','
        ) . '%';
    }
}
