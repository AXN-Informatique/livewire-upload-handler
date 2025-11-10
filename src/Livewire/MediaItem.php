<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire;

use Illuminate\Support\Arr;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Locked;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Isolate]
class MediaItem extends Item
{
    #[Locked]
    public HasMedia $model;

    #[Locked]
    public string $mediaCollection = 'default';

    #[Locked]
    public array $mediaFilters = [];

    #[Locked]
    public ?Media $media = null;

    public function mount(): void
    {
        parent::mount();

        if ($this->acceptsMimeTypes === []) {
            $this->acceptsMimeTypes = $this->model->getMediaCollection($this->mediaCollection)->acceptsMimeTypes;
        }

        $this->maxFileSize ??= config('media-library.max_file_size');

        if (! $this->onlyUpload && ! $this->attachedToGroup) {
            $this->media = $this->model->getFirstMedia($this->mediaCollection);
            $this->itemData['id'] = $this->media?->id;
        }
    }

    public function deleteSavedFile(): void
    {
        $this->dispatch(
            'livewire-upload-handler:media-deleted',
            mediaId: $this->media->id,
        );

        $this->media->delete();
        $this->media = null;

        $this->itemData['id'] = null;
    }

    protected function hasSavedFile(): bool
    {
        return $this->media instanceof Media;
    }

    protected function savedFileDisk(): string
    {
        return $this->media->disk;
    }

    protected function savedFilePath(): string
    {
        return $this->media->getPathRelativeToRoot();
    }

    protected function savedFileName(): string
    {
        return $this->media->file_name;
    }

    protected function savedFileMimeType(): string
    {
        return $this->media->mime_type;
    }

    protected function saveUploadedFile(TemporaryUploadedFile $uploadedFile): void
    {
        $customProperties = $this->mediaFilters;

        if ($this->media !== null) {
            $customProperties = $this->media->custom_properties;
            $this->media->delete();
        }

        $media = $this->model
            ->addMedia($uploadedFile)
            ->setOrder($this->itemData['order'] ?? null)
            ->withCustomProperties($customProperties)
            ->toMediaCollection($this->mediaCollection);

        if (! $this->onlyUpload) {
            $this->media = $media;
            $this->itemData['id'] = $media->id;
        }

        $this->dispatch(
            'livewire-upload-handler:media-saved',
            mediaId: $media->id,
        );
    }
}
