<?php

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
    public ?array $mediaProperties = null;

    protected array $medias = [];

    public function mount(): void
    {
        parent::mount();

        $this->mediaCollection ??= $this->propertyValueFromItem('mediaCollection');
        $this->mediaProperties ??= $this->propertyValueFromItem('mediaProperties');

        if ($this->acceptsMimeTypes === []) {
            $this->acceptsMimeTypes = $this->model->getMediaCollection($this->mediaCollection)->acceptsMimeTypes;
        }

        $this->maxFileSize ??= config('media-library.max_file_size');

        if (! $this->onlyUpload) {
            $itemsIdsByMediaId = collect($this->items)
                ->whereNotNull('id')
                ->mapWithKeys(fn ($itemData, $itemId): array => [$itemData['id'] => $itemId])
                ->all();

            foreach ($this->model->getMedia($this->mediaCollection, $this->mediaProperties) as $media) {
                $itemId = $itemsIdsByMediaId[$media->id] ?? $this->addItem([
                    'id' => $media->id,
                    'order' => $media->order_column,
                ]);

                $this->medias[$itemId] = $media;
            }
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

    protected function itemComponentParams(string $itemId): array
    {
        return [
            ...parent::itemComponentParams($itemId),
            'model' => $this->model,
            'mediaCollection' => $this->mediaCollection,
            'mediaProperties' => $this->mediaProperties,
            'media' => $this->medias[$itemId] ?? null,
        ];
    }
}
