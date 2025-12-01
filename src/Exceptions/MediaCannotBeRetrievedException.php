<?php

namespace Axn\LivewireUploadHandler\Exceptions;

use Exception;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaCannotBeRetrievedException extends Exception
{
    public static function doesNotBelongToModel(HasMedia $model, mixed $mediaId): self
    {
        $modelClass = $model::class;

        return new self("Media with id `{$mediaId}` does not exist or does not belong to model {$modelClass} with id {$model->getKey()}");
    }

    public static function doesNotBelongToCollection(string $collectionName, Media $media): self
    {
        return new self("Media id {$media->getKey()} is not part of collection `{$collectionName}`");
    }
}
