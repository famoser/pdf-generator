<?php

namespace Famoser\PdfGenerator\Frontend\Layout\Style;

enum ColumnSize: string
{
    public const UNIT = '*';

    public static function parseUnit(string $value): float
    {
        if (self::UNIT === $value) {
            return 1;
        }

        return floatval($value);
    }

    public static function isUnit(ColumnSize|float|string $value): bool
    {
        return str_ends_with($value, self::UNIT);
    }

    case AUTO = 'auto';
    case MINIMAL = 'min';
}
