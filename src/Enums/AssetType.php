<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Enums;

enum AssetType: string
{
    case JavaScript = 'application/javascript';
    case CSS = 'text/css';

    public static function fromFilename(string $filename): ?self
    {
        return match (true) {
            str_ends_with($filename, '.js') => self::JavaScript,
            str_ends_with($filename, '.css') => self::CSS,
            default => null,
        };
    }

    public function mimeType(): string
    {
        return $this->value;
    }

    public function withCharset(): string
    {
        return $this->value.'; charset=utf-8';
    }
}
