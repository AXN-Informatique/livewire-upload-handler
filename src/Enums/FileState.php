<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Enums;

enum FileState: string
{
    case Uploading = 'uploading';
    case Uploaded = 'uploaded';
    case Saved = 'saved';
    case Error = 'error';
    case Deleted = 'deleted';

    public function isUploading(): bool
    {
        return $this === self::Uploading;
    }

    public function isComplete(): bool
    {
        return $this === self::Uploaded || $this === self::Saved;
    }

    public function hasError(): bool
    {
        return $this === self::Error;
    }
}
