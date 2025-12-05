<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire;

use Axn\LivewireUploadHandler\Livewire\Concerns\MediaCommon;

class MediaGroup extends Group
{
    use MediaCommon;

    protected array $medias = [];

    public function mount(): void
    {
        parent::mount();

        $mediaCollectionSizeLimit = $this->model->getMediaCollection($this->mediaCollection)->collectionSizeLimit ?: 0;

        if ($this->maxFilesNumber === 0 || ($mediaCollectionSizeLimit > 0 && $this->maxFilesNumber > $mediaCollectionSizeLimit)) {
            $this->maxFilesNumber = $mediaCollectionSizeLimit;
        }
    }

    protected function loadInitialItemsData(): void
    {
        $medias = $this->model->getMedia($this->mediaCollection, $this->mediaFilters);

        if (old() !== []) {
            $order = 1;

            foreach ($this->old() as $itemId => $old) {
                $media = null;

                if (isset($old['id'])) {
                    $media = $medias->where('id', $old['id'])->first();
                    $this->medias[$itemId] = $media;
                }

                $this->items[$itemId] = [
                    'id' => $media->id ?? null,
                    'order' => $order++,
                    'deleted' => ! empty($old['deleted']),
                    ...$this->initialItemData($old, $media),
                ];
            }
        } else {
            foreach ($medias as $media) {
                $itemId = $this->addItem([
                    'id' => $media->id,
                    'order' => $media->order_column,
                    'deleted' => false,
                    ...$this->initialItemData([], $media),
                ]);

                $this->medias[$itemId] = $media;
            }
        }
    }

    protected function saveFileOrder(string|int $id, int $order): void
    {
        $this->model->media()
            ->whereKey($id)
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
            ...$this->publicPropsFrom(MediaCommon::class),
            'media' => $this->medias[$itemId] ?? null,
        ];
    }
}
