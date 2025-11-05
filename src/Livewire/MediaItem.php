<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire;

use Axn\LivewireUploadHandler\Enums\FileType;
use Axn\LivewireUploadHandler\GlideServerFactory;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Locked;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

#[Isolate]
class MediaItem extends Item
{
    #[Locked]
    public HasMedia $model;

    #[Locked]
    public string $mediaCollection = 'default';

    #[Locked]
    public array $mediaProperties = [];

    #[Locked]
    public ?Media $media = null;

    public function mount(): void
    {
        parent::mount();

        if ($this->acceptsMimeTypes === []) {
            $this->acceptsMimeTypes = $this->model->getMediaCollection($this->mediaCollection)->acceptsMimeTypes;
        }

        $this->maxFileSize ??= config('media-library.max_file_size');

        if (! $this->onlyUpload && ! $this->attachedToGroup && $this->model->hasMedia($this->mediaCollection)) {
            $this->media = $this->model->getFirstMedia($this->mediaCollection);
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

    protected function savedFileId(): string
    {
        return (string) $this->media->id;
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
        $media = $this->model
            ->addMedia($uploadedFile)
            ->setOrder($this->itemData['order'] ?? null)
            ->withCustomProperties($this->mediaProperties)
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
