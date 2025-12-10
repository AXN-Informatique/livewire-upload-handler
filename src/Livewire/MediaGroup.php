<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire;

use Axn\LivewireUploadHandler\Livewire\Concerns\MediaCommon;
use Illuminate\Support\Collection;

class MediaGroup extends Group
{
    use MediaCommon;

    public function mount(): void
    {
        parent::mount();

        $mediaCollectionSizeLimit = $this->model->getMediaCollection($this->mediaCollection)->collectionSizeLimit ?: 0;

        if ($this->maxFilesNumber <= 0 || ($mediaCollectionSizeLimit > 0 && $this->maxFilesNumber > $mediaCollectionSizeLimit)) {
            $this->maxFilesNumber = $mediaCollectionSizeLimit;
        }
    }

    protected function initialEntities(): array|Collection
    {
        return $this->model
            ->getMedia($this->mediaCollection, $this->mediaFilters)
            ->keyBy('id');
    }

    protected function saveFileOrder(string|int $id, int $order): void
    {
        $this->retrieveMedia($id)->update([
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
        ];
    }
}
