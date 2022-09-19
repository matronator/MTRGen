<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Template;

class Filters
{
    public const ENCODING = 'UTF-8';

    public static function upper(string $string): string
    {
        return mb_strtoupper($string, static::ENCODING);
    }

    public static function lower(string $string): string
    {
        return mb_strtolower($string, static::ENCODING);
    }

    public static function upperFirst(string $string): string
    {
        $fc = mb_strtoupper(mb_substr($string, 0, 1, static::ENCODING), static::ENCODING);
        return $fc . mb_substr($string, 1, null, static::ENCODING);
    }
}