<?php

namespace Axn\LivewireUploadHandler;

function bytes_to_int(string|int $value): int
{
    $intValue = (int) $value;

    if (is_numeric($value)) {
        return $intValue;
    }

    $unit = strtoupper(substr($value, -1));

    return match ($unit) {
        'G' => $intValue * 1024 * 1024 * 1024,
        'M' => $intValue * 1024 * 1024,
        'K' => $intValue * 1024,
        default => $intValue,
    };
}

function str_arr_to_dot(string $value): string
{
    return str_replace(['[', ']'], ['.', ''], $value);
}
