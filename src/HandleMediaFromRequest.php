<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler;

use Axn\LivewireUploadHandler\Exceptions\MediaCannotBeRetrievedException;
use Axn\LivewireUploadHandler\Exceptions\MediaCollectionNotRegisteredException;
use Closure;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class HandleMediaFromRequest
{
    public function single(
        ?array $data,
        HasMedia $model,
        string $mediaCollection = 'default',
        ?Closure $customizeMedia = null,
        ?int $order = null,
    ): void {
        if (empty($data['tmpName']) && empty($data['id'])) {
            return;
        }

        if (! $model->getMediaCollection($mediaCollection) instanceof MediaCollection) {
            throw MediaCollectionNotRegisteredException::make($model, $mediaCollection);
        }

        if (! empty($data['deleted']) && ! empty($data['id'])) {
            $mediaToDelete = $this->retrieveMedia($model, $mediaCollection, $data['id']);
            $mediaToDelete->delete();

            return;
        }

        if (! empty($data['tmpName'])) {
            $customProperties = [];

            if (! empty($data['id'])) {
                $mediaToReplace = $this->retrieveMedia($model, $mediaCollection, $data['id']);
                $customProperties = $mediaToReplace->custom_properties;
                $mediaToReplace->delete();
            }

            $tmpFile = TemporaryUploadedFile::createFromLivewire($data['tmpName']);

            $media = $model
                ->addMedia($tmpFile)
                ->setOrder($order)
                ->withCustomProperties($customProperties)
                ->toMediaCollection($mediaCollection);

        } else {
            $media = $this->retrieveMedia($model, $mediaCollection, $data['id']);
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
    ): void {
        if ($data === null || $data === []) {
            return;
        }

        $order = 1;

        foreach ($data as $itemData) {
            $this->single($itemData, $model, $mediaCollection, $customizeMedia, $order++);
        }
    }

    protected function retrieveMedia(
        HasMedia $model,
        string $mediaCollection,
        mixed $mediaId,
    ): Media {
        $media = $model->media()->find($mediaId);

        if (! $media instanceof Media) {
            throw MediaCannotBeRetrievedException::doesNotBelongToModel($model, $mediaId);
        }

        if ($media->collection_name !== $mediaCollection) {
            throw MediaCannotBeRetrievedException::doesNotBelongToCollection($mediaCollection, $media);
        }

        return $media;
    }
}
