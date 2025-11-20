<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire;

use Axn\LivewireUploadHandler\Livewire\Concerns\MediaCommon;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Locked;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Isolate]
class MediaItem extends Item
{
    use MediaCommon;

    #[Locked]
    public ?Media $media = null;

    protected function loadInitialItemData(): void
    {
        $old = $this->loadUploadedFileFromOldThenGetOld();

        if ($old === null) {
            return;
        }

        $this->media = $this->model->getFirstMedia($this->mediaCollection);

        $this->itemData = [
            'id' => $this->media->id ?? null,
            'deleted' => ! empty($old['deleted']),
            ...$this->initialItemData($old, $this->media),
        ];
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

    protected function savedFileSize(): int
    {
        return (int) $this->media->size;
    }

    protected function savedFileMimeType(): string
    {
        return $this->media->mime_type;
    }

    protected function saveUploadedFile(TemporaryUploadedFile $uploadedFile): void
    {
        $customProperties = $this->mediaFilters;

        if ($this->media instanceof Media) {
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
