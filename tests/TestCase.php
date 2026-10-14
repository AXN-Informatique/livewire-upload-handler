<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Tests;

use Axn\LivewireUploadHandler\ServiceProvider;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

abstract class TestCase extends Orchestra
{
    use LazilyRefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            MediaLibraryServiceProvider::class,
            ServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Fixtures/migrations');
    }

    /**
     * Store a file in Livewire's temporary upload directory, the way an
     * assembled chunked upload is stored, and return its temporary name.
     */
    protected function storeTemporaryFile(string $originalName, string $content): string
    {
        $tmpName = TemporaryUploadedFile::generateHashNameWithOriginalNameEmbedded(
            UploadedFile::fake()->create($originalName)
        );

        FileUploadConfiguration::storage()->put(FileUploadConfiguration::path($tmpName), $content);

        return $tmpName;
    }

    /**
     * @return array<string>
     */
    protected function temporaryDirectoryFiles(): array
    {
        return FileUploadConfiguration::storage()->files(FileUploadConfiguration::directory());
    }
}
