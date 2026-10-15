<?php

namespace App\Support;

class CatalogDescriptions
{
    public static function commodity(string $name): string
    {
        return CatalogCopy::commodity($name) ?? $name . ' untuk aneka kreasi dapur rumahan. Hubungi koperasi untuk informasi ketersediaan.';
    }

    public static function product(string $name): ?string
    {
        return CatalogCopy::product($name);
    }
}
