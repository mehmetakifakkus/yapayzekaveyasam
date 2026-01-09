<?php

/**
 * Convert Turkish characters to ASCII equivalents
 *
 * @param string $text
 * @return string
 */
function turkish_to_ascii(string $text): string
{
    $turkish = ['ş', 'Ş', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'ç', 'Ç', 'ı', 'İ'];
    $ascii   = ['s', 'S', 'g', 'G', 'u', 'U', 'o', 'O', 'c', 'C', 'i', 'I'];

    return str_replace($turkish, $ascii, $text);
}

/**
 * Generate URL-friendly slug with Turkish character support
 *
 * @param string $text
 * @return string
 */
function turkish_slug(string $text): string
{
    $text = turkish_to_ascii($text);
    return url_title($text, '-', true);
}
