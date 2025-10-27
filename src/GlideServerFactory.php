<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler;

use Axn\LaravelGlide\GlideServer;
use Illuminate\Support\Facades\Storage;

class GlideServerFactory
{
    /**
     * @var array<string, GlideServer>
     */
    protected static array $servers = [];

    public static function forDisk(string $disk): GlideServer
    {
        if (\array_key_exists($disk, self::$servers)) {
            return self::$servers[$disk];
        }

        $config = config('livewire-upload-handler');

        self::$servers[$disk] = new GlideServer(app(), [
            'source' => Storage::disk($disk)->getDriver(),
            'source_path_prefix' => '',
            'cache' => Storage::getDriver(),
            'cache_path_prefix' => '.livewire-upload-handler-glide-cache/'.$disk,
            'signatures' => true,
            'driver' => $config['glide_image_driver'],
            'max_image_size' => $config['glide_max_image_size'],
            'sign_key' => $config['glide_sign_key'],
            'base_url' => $config['glide_base_url'].'/'.$disk,
        ]);

        return self::$servers[$disk];
    }

    /**
     * Clear all cached server instances.
     */
    public static function clearCache(): void
    {
        self::$servers = [];
    }

    /**
     * Check if a server instance exists for the given disk.
     */
    public static function has(string $disk): bool
    {
        return \array_key_exists($disk, self::$servers);
    }
}
