<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire;

use Livewire\Attributes\Locked;
use Spatie\MediaLibrary\HasMedia;

class MediaGroup extends Group
{
    #[Locked]
    public HasMedia $model;

    #[Locked]
    public ?string $mediaCollection = null;

    #[Locked]
    public ?array $mediaFilters = null;

    protected array $medias = [];

    public function mount(): void
    {
        parent::mount();

        $this->mediaCollection ??= $this->propertyValueFromItem('mediaCollection');
        $this->mediaFilters ??= $this->propertyValueFromItem('mediaFilters');

        if ($this->acceptsMimeTypes === []) {
            $this->acceptsMimeTypes = $this->model->getMediaCollection($this->mediaCollection)->acceptsMimeTypes;
        }

        $this->maxFileSize ??= config('media-library.max_file_size');

        if (! $this->onlyUpload) {
            $this->loadExistingMedia();
        }
    }

    /**
     * Load existing media items from the model.
     */
    protected function loadExistingMedia(): void
    {
        $itemsIdsByMediaId = collect($this->items)
            ->whereNotNull('id')
            ->mapWithKeys(fn (array $itemData, string $itemId): array => [$itemData['id'] => $itemId])
            ->all();

        foreach ($this->model->getMedia($this->mediaCollection, $this->mediaFilters) as $media) {
            $itemId = $itemsIdsByMediaId[$media->id] ?? $this->addItem([
                'id' => $media->id,
                'order' => $media->order_column,
            ]);

            $this->medias[$itemId] = $media;
        }
    }

    protected function saveItemOrder(string $itemId, int $order): void
    {
        $mediaId = $this->items[$itemId]['id'] ?? null;

        if ($mediaId === null) {
            return;
        }

        $this->model->media()
            ->whereKey($mediaId)
            ->update([
                'order_column' => $order,
            ]);
    }

    protected function itemComponentClassName(): string
    {
        return MediaItem::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function itemComponentParams(string $itemId): array
    {
        return [
            ...parent::itemComponentParams($itemId),
            'model' => $this->model,
            'mediaCollection' => $this->mediaCollection,
            'mediaFilters' => $this->mediaFilters,
            'media' => $this->medias[$itemId] ?? null,
        ];
    }
}
