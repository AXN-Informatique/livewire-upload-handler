<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Enums;

enum MediaType: string
{
    case Image = 'image';
    case Video = 'video';
    case Audio = 'audio';
    case Document = 'document';
    case Archive = 'archive';
    case Other = 'other';

    public static function fromMimeType(string $mimeType): self
    {
        return match (true) {
            str_starts_with($mimeType, 'image/') => self::Image,
            str_starts_with($mimeType, 'video/') => self::Video,
            str_starts_with($mimeType, 'audio/') => self::Audio,
            \in_array($mimeType, [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'text/plain',
                'text/csv',
            ], true) => self::Document,
            \in_array($mimeType, [
                'application/zip',
                'application/x-rar-compressed',
                'application/x-7z-compressed',
                'application/gzip',
                'application/x-tar',
            ], true) => self::Archive,
            default => self::Other,
        };
    }

    public function isImage(): bool
    {
        return $this === self::Image;
    }

    public function isVideo(): bool
    {
        return $this === self::Video;
    }

    public function isAudio(): bool
    {
        return $this === self::Audio;
    }

    public function isDocument(): bool
    {
        return $this === self::Document;
    }

    public function isArchive(): bool
    {
        return $this === self::Archive;
    }

    public function supportsPreview(): bool
    {
        return $this === self::Image || $this === self::Video;
    }
}
