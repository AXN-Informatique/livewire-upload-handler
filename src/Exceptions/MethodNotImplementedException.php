<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Exceptions;

use LogicException;

class MethodNotImplementedException extends LogicException
{
    public static function saveUploadedFile(string $componentClass): self
    {
        return new self(
            \sprintf('`saveUploadedFile` method not implemented in %s. ', $componentClass).
            'Either extend this component and implement the method, or use the MediaItem component for automatic Spatie Media Library integration.'
        );
    }

    public static function deleteSavedFile(string $componentClass): self
    {
        return new self(
            \sprintf('`deleteSavedFile` method not implemented in %s. ', $componentClass).
            'Either extend this component and implement the method, or use the MediaItem component for automatic Spatie Media Library integration.'
        );
    }

    public static function downloadSavedFile(string $componentClass): self
    {
        return new self(
            \sprintf('`downloadSavedFile` method not implemented in %s. ', $componentClass).
            'Either extend this component and implement the method, or use the MediaItem component for automatic Spatie Media Library integration.'
        );
    }

    public static function saveFileOrder(string $componentClass): self
    {
        return new self(
            \sprintf('`saveFileOrder` method not implemented in %s. ', $componentClass).
            'Either extend this component and implement the method, or use the MediaGroup component for automatic Spatie Media Library integration.'
        );
    }

    public static function savedFileDisk(string $componentClass): self
    {
        return new self(
            \sprintf('`savedFileDisk` method not implemented in %s. ', $componentClass).
            'Either extend this component and implement the method, or use the MediaGroup component for automatic Spatie Media Library integration.'
        );
    }

    public static function savedFilePath(string $componentClass): self
    {
        return new self(
            \sprintf('`savedFilePath` method not implemented in %s. ', $componentClass).
            'Either extend this component and implement the method, or use the MediaGroup component for automatic Spatie Media Library integration.'
        );
    }

    public static function savedFileName(string $componentClass): self
    {
        return new self(
            \sprintf('`savedFileName` method not implemented in %s. ', $componentClass).
            'Either extend this component and implement the method, or use the MediaGroup component for automatic Spatie Media Library integration.'
        );
    }

    public static function savedFileSize(string $componentClass): self
    {
        return new self(
            \sprintf('`savedFileSize` method not implemented in %s. ', $componentClass).
            'Either extend this component and implement the method, or use the MediaGroup component for automatic Spatie Media Library integration.'
        );
    }

    public static function savedFileMimeType(string $componentClass): self
    {
        return new self(
            \sprintf('`savedFileMimeType` method not implemented in %s. ', $componentClass).
            'Either extend this component and implement the method, or use the MediaGroup component for automatic Spatie Media Library integration.'
        );
    }
}
