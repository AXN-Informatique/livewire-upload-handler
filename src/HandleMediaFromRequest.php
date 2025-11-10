<?php

namespace Axn\LivewireUploadHandler;

use Closure;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\MediaLibrary\HasMedia;

class HandleMediaFromRequest
{
    public function single(
        ?array $data,
        HasMedia $model,
        string $mediaCollection = 'default',
        ?Closure $customizeMedia = null,
        ?int $order = null,
    ): void
    {
        if (! empty($data['deleted']) && ! empty($data['id'])) {
            $model->deleteMedia($data['id']);
            return;
        }

        if (! empty($data['tmpName'])) {
            $customProperties = [];

            if (! empty($data['id'])) {
                $customProperties = $model->media()->whereKey($data['id'])->value('custom_properties');
                $model->deleteMedia($data['id']);
            }

            $tmpFile = TemporaryUploadedFile::createFromLivewire($data['tmpName']);

            $media = $model
                ->addMedia($tmpFile)
                ->setOrder($order)
                ->withCustomProperties($customProperties)
                ->toMediaCollection($mediaCollection);

        } else {
            $media = $model->media()->findOrFail($data['id']);
            $media->order_column = $order ?? $media->order_column;
        }

        if ($customizeMedia instanceof Closure) {
            $customizeMedia($media, $data);
        }

        $media->save();
    }

    public function multiple(
        ?array $data,
        HasMedia $model,
        string $mediaCollection = 'default',
        ?Closure $customizeMedia = null,
    ): void
    {
        $order = 1;

        foreach ($data as $itemData) {
            $this->single($itemData, $model, $mediaCollection, $customizeMedia, $order++);
        }
    }
}
