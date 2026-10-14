<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TestModel extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'test_models';

    protected $guarded = [];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('single')
            ->singleFile();

        $this->addMediaCollection('documents');

        $this->addMediaCollection('limited')
            ->onlyKeepLatest(3);

        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png']);
    }
}
