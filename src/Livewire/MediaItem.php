<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire;

use Axn\LivewireUploadHandler\Livewire\Concerns\MediaCommon;
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
            'luh-media-deleted',
            inputBaseName: $this->inputBaseNameWithoutItemId(),
            mediaId: $this->itemData['id'],
        );

        $this->retrieveMedia($this->itemData['id'])->delete();

        $this->itemData['id'] = null;
        $this->savedFileDisk = null;
        $this->savedFilePath = null;
    }

    protected function saveUploadedFile(TemporaryUploadedFile $uploadedFile): void
    {
        $customProperties = $this->mediaFilters;

        if ($this->hasSavedFile()) {
            $media = $this->retrieveMedia($this->itemData['id']);
            $customProperties = $media->custom_properties;
            $media->delete();
        }

        $media = $this->model
            ->addMedia($uploadedFile)
            ->setOrder($this->itemData['order'] ?? null)
            ->withCustomProperties($customProperties)
            ->toMediaCollection($this->mediaCollection);

        if (! $this->onlyUpload) {
            $this->itemData['id'] = $media->id;
            $this->savedFileDisk = $media->disk;
            $this->savedFilePath = $media->getPathRelativeToRoot();
        }

        $this->dispatch(
            'luh-media-saved',
            inputBaseName: $this->inputBaseNameWithoutItemId(),
            mediaId: $media->id,
        );
    }
}
