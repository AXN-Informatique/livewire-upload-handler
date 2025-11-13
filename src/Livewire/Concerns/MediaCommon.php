<?php

namespace Axn\LivewireUploadHandler\Livewire\Concerns;

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

        $this->maxFileSize ??= config('media-library.max_file_size');
    }

    protected function initialItemData(array $old = [], ?Media $media = null): array
    {
        return [];
    }
}
