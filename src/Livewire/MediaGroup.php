<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire;

use Axn\LivewireUploadHandler\Livewire\Concerns\MediaCommon;

class MediaGroup extends Group
{
    use MediaCommon;

    protected array $medias = [];

    protected function loadInitialItemsData(): void
    {
        if ($this->onlyUpload) {
            return;
        }

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
