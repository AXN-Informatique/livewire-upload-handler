<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Exceptions;

use LogicException;

class FileNotHandledException extends LogicException
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

    public static function saveItemOrder(string $componentClass): self
    {
        return new self(
            \sprintf('`saveItemOrder` method not implemented in %s. ', $componentClass).
            'Either extend this component and implement the method, or use the MediaGroup component for automatic Spatie Media Library integration.'
        );
    }
}
