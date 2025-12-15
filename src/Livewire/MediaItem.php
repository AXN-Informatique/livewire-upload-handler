<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire;

use Axn\LivewireUploadHandler\Livewire\Concerns\MediaCommon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Isolate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Isolate]
class MediaItem extends Item
{
    use MediaCommon;

    protected function initialEntity(): ?Media
    {
        return $this->model->getFirstMedia($this->mediaCollection);
    }

    public function deleteSavedFile(): void
    {
        $this->dispatch(
            'livewire-upload-handler:media-deleted',
            mediaId: $this->itemData['id'],
        );

        $this->media->delete();

        $this->itemData['id'] = null;
        $this->savedFileDisk = null;
        $this->savedFilePath = null;
    }

    protected function saveUploadedFile(TemporaryUploadedFile $uploadedFile): void
    {
        $customProperties = $this->mediaFilters;

        if ($this->hasSavedFile()) {
            $customProperties = $this->media->custom_properties;
            $this->media->delete();
        }

        $media = $this->model
            ->addMedia($uploadedFile)
            ->setOrder($this->itemData['order'] ?? null)
            ->withCustomProperties($customProperties)
            ->toMediaCollection($this->mediaCollection);

        $this->mediaSaved($media);

        if (! $this->onlyUpload) {
            $this->itemData['id'] = $media->id;
            $this->savedFileDisk = $media->disk;
            $this->savedFilePath = $media->getPathRelativeToRoot();
        }

        $this->dispatch(
            'livewire-upload-handler:media-saved',
            mediaId: $media->id,
        );
    }

    protected function mediaSaved(Media $media): void
    {
        //
    }

    #[Computed]
    protected function media(): ?Media
    {
        if (! isset($this->itemData['id'])) {
            return null;
        }

        return $this->retrieveMedia($this->itemData['id']);
    }
}
