<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Tests\Feature;

use Axn\LivewireUploadHandler\HandleMediaFromRequest;
use Axn\LivewireUploadHandler\Tests\Fixtures\TestModel;
use Axn\LivewireUploadHandler\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class HandleMediaFromRequestTest extends TestCase
{
    public function test_single_with_temporary_file_adds_media_with_original_name(): void
    {
        Storage::fake('public');
        $model = TestModel::create();
        $tmpName = $this->storeTemporaryFile('report.txt', 'hello world');

        new HandleMediaFromRequest()->single(['tmpName' => $tmpName], $model, 'single');

        $media = $model->getFirstMedia('single');
        $this->assertSame('report.txt', $media->file_name);
        $this->assertSame('hello world', Storage::disk('public')->get($media->getPathRelativeToRoot()));
    }

    public function test_single_with_temporary_file_and_id_replaces_media_and_keeps_its_custom_properties(): void
    {
        Storage::fake('public');
        $model = TestModel::create();
        $existingMedia = $model->addMedia(UploadedFile::fake()->createWithContent('old.txt', 'old'))
            ->withCustomProperties(['caption' => 'Kept'])
            ->toMediaCollection('documents');
        $tmpName = $this->storeTemporaryFile('new.txt', 'new');

        new HandleMediaFromRequest()->single(['id' => $existingMedia->id, 'tmpName' => $tmpName], $model, 'documents');

        $media = $model->refresh()->getFirstMedia('documents');
        $this->assertModelMissing($existingMedia);
        $this->assertSame('new.txt', $media->file_name);
        $this->assertSame('Kept', $media->getCustomProperty('caption'));
    }

    public function test_single_with_deleted_flag_deletes_media(): void
    {
        Storage::fake('public');
        $model = TestModel::create();
        $media = $model->addMedia(UploadedFile::fake()->createWithContent('old.txt', 'old'))
            ->toMediaCollection('documents');

        new HandleMediaFromRequest()->single(['id' => $media->id, 'deleted' => '1'], $model, 'documents');

        $this->assertModelMissing($media);
    }

    public function test_multiple_saves_media_order_from_submitted_order(): void
    {
        Storage::fake('public');
        $model = TestModel::create();
        $existingMedia = $model->addMedia(UploadedFile::fake()->createWithContent('existing.txt', 'existing'))
            ->setOrder(1)
            ->toMediaCollection('documents');
        $tmpName = $this->storeTemporaryFile('new.txt', 'new');

        new HandleMediaFromRequest()->multiple([
            '_a' => ['tmpName' => $tmpName],
            '_b' => ['id' => $existingMedia->id],
        ], $model, 'documents');

        $this->assertSame(
            ['new.txt' => 1, 'existing.txt' => 2],
            $model->refresh()->getMedia('documents')->pluck('order_column', 'file_name')->all(),
        );
    }
}
