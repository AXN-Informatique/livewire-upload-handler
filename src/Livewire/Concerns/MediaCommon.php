<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire\Concerns;

use Axn\LivewireUploadHandler\Exceptions\MediaCannotBeRetrievedException;
use Livewire\Attributes\Locked;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait MediaCommon
{
    #[Locked]
    public HasMedia $model;

    #[Locked]
    public string $mediaCollection = 'default';

    #[Locked]
    public array $mediaFilters = [];

    public function mountMediaCommon(): void
    {
        if ($this->acceptsMimeTypes === []) {
            $this->acceptsMimeTypes = $this->model->getMediaCollection($this->mediaCollection)->acceptsMimeTypes;
        }

        if ($this->maxFileSize <= 0 || $this->maxFileSize > config('media-library.max_file_size')) {
            $this->maxFileSize = config('media-library.max_file_size');
        }
    }

    protected function initialItemData(array $old = [], ?Media $media = null): array
    {
        return [
            'id' => $media?->id,
            'order' => $media?->order_column,
            'deleted' => ! empty($old['deleted']),
        ];
    }

    protected function initialItemParams(?Media $media = null): array
    {
        return [
            'savedFileDisk' => $media->disk ?? $this->savedFileDisk,
            'savedFilePath' => $media?->getPathRelativeToRoot(),
        ];
    }

    protected function retrieveMedia(int $mediaId): Media
    {
        $media = $this->model->media()->find($mediaId);

        if (! $media instanceof Media) {
            throw MediaCannotBeRetrievedException::doesNotBelongToModel($this->model, $mediaId);
        }

        if ($media->collection_name !== $this->mediaCollection) {
            throw MediaCannotBeRetrievedException::doesNotBelongToCollection($this->mediaCollection, $media);
        }

        return $media;
    }
}
