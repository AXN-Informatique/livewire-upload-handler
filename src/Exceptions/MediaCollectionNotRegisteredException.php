<?php

namespace Axn\LivewireUploadHandler\Exceptions;

use Exception;
use Spatie\MediaLibrary\HasMedia;

class MediaCollectionNotRegisteredException extends Exception
{
    public static function make(HasMedia $model, string $collectionName): self
    {
        $modelClass = $model::class;

        return new self(\sprintf('Media collection `%s` is not registered in model `%s`.', $collectionName, $modelClass));
    }
}
