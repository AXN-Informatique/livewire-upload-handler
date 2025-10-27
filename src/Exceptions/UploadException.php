<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Exceptions;

use RuntimeException;
use Throwable;

class UploadException extends RuntimeException
{
    public static function chunkProcessingFailed(Throwable $previous): self
    {
        return new self(
            'Failed to process file chunk during upload.',
            0,
            $previous
        );
    }

    public static function validationFailed(string $message): self
    {
        return new self('File validation failed: '.$message);
    }

    public static function fileNotFound(string $filename): self
    {
        return new self('Uploaded file not found: '.$filename);
    }

    public static function invalidMimeType(string $mimeType, array $allowedTypes): self
    {
        $allowed = implode(', ', $allowedTypes);

        return new self(\sprintf("Invalid MIME type '%s'. Allowed types: %s", $mimeType, $allowed));
    }

    public static function fileTooLarge(int $size, int $maxSize): self
    {
        $sizeMB = round($size / 1024 / 1024, 2);
        $maxSizeMB = round($maxSize / 1024 / 1024, 2);

        return new self(\sprintf('File size (%sMB) exceeds maximum allowed size (%sMB)', $sizeMB, $maxSizeMB));
    }
}
