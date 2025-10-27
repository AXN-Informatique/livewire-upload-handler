<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Exceptions;

use LogicException;

class FileNotHandledException extends LogicException
{
    public static function saveUploadedFile(string $componentClass): self
    {
        return new self(
            "`saveUploadedFile` method not implemented in {$componentClass}. ".
            'Either extend this component and implement the method, or use the MediaItem component for automatic Spatie Media Library integration.'
        );
    }

    public static function deleteSavedFile(string $componentClass): self
    {
        return new self(
            "`deleteSavedFile` method not implemented in {$componentClass}. ".
            'Either extend this component and implement the method, or use the MediaItem component for automatic Spatie Media Library integration.'
        );
    }

    public static function downloadSavedFile(string $componentClass): self
    {
        return new self(
            "`downloadSavedFile` method not implemented in {$componentClass}. ".
            'Either extend this component and implement the method, or use the MediaItem component for automatic Spatie Media Library integration.'
        );
    }

    public static function saveItemOrder(string $componentClass): self
    {
        return new self(
            "`saveItemOrder` method not implemented in {$componentClass}. ".
            'Either extend this component and implement the method, or use the MediaGroup component for automatic Spatie Media Library integration.'
        );
    }
}
