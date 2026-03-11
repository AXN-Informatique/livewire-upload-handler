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

        return new self(\sprintf('Media with id `%s` does not exist or does not belong to model `%s` with id `%s`.', $mediaId, $modelClass, $model->getKey()));
    }

    public static function doesNotBelongToCollection(string $collectionName, Media $media): self
    {
        return new self(\sprintf('Media id `%s` is not part of collection `%s`.', $media->getKey(), $collectionName));
    }
}
