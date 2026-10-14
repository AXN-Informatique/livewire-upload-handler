<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Tests\Feature\Livewire;

use Axn\LivewireUploadHandler\Exceptions\MediaCollectionNotRegisteredException;
use Axn\LivewireUploadHandler\Livewire\MediaItem;
use Axn\LivewireUploadHandler\Tests\Fixtures\TestModel;
use Axn\LivewireUploadHandler\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Throwable;

class MediaItemTest extends TestCase
{
    public function test_auto_save_upload_adds_media_to_collection_and_dispatches_media_saved_event(): void
    {
        Storage::fake('public');
        $model = TestModel::create();

        $component = Livewire::test(MediaItem::class, [
            'model' => $model,
            'mediaCollection' => 'single',
            'inputBaseName' => 'avatar',
            'autoSave' => true,
        ])
            ->set('uploadingFileSize', 11)
            ->upload('chunkFile', [UploadedFile::fake()->createWithContent('report.txt', 'hello world')]);

        $media = $model->refresh()->getFirstMedia('single');
        $this->assertSame('report.txt', $media->file_name);
        $this->assertSame('hello world', Storage::disk('public')->get($media->getPathRelativeToRoot()));
        $component
            ->assertSet('itemData.id', $media->id)
            ->assertSet('savedFilePath', $media->getPathRelativeToRoot())
            ->assertSet('uploadedFile', null)
            ->assertDispatched('luh-media-saved', inputBaseName: 'avatar', mediaId: $media->id)
            ->assertNotDispatched('luh-uploaded');
    }

    public function test_auto_save_upload_replaces_existing_media_and_keeps_its_custom_properties(): void
    {
        Storage::fake('public');
        $model = TestModel::create();
        $existingMedia = $model->addMedia(UploadedFile::fake()->createWithContent('old.txt', 'old'))
            ->withCustomProperties(['caption' => 'Kept'])
            ->toMediaCollection('single');

        Livewire::test(MediaItem::class, [
            'model' => $model,
            'mediaCollection' => 'single',
            'autoSave' => true,
        ])
            ->set('uploadingFileSize', 3)
            ->upload('chunkFile', [UploadedFile::fake()->createWithContent('new.txt', 'new')]);

        $media = $model->refresh()->getFirstMedia('single');
        $this->assertModelMissing($existingMedia);
        $this->assertSame('new.txt', $media->file_name);
        $this->assertSame('Kept', $media->getCustomProperty('caption'));
    }

    public function test_mount_loads_saved_media_of_collection(): void
    {
        Storage::fake('public');
        $model = TestModel::create();
        $media = $model->addMedia(UploadedFile::fake()->createWithContent('saved.txt', 'saved'))
            ->toMediaCollection('single');

        $component = Livewire::test(MediaItem::class, [
            'model' => $model,
            'mediaCollection' => 'single',
        ]);

        $component
            ->assertSet('itemData.id', $media->id)
            ->assertSet('savedFileDisk', 'public')
            ->assertSet('savedFilePath', $media->getPathRelativeToRoot())
            ->assertSee('saved.txt');
    }

    public function test_deleting_saved_file_removes_media_and_dispatches_media_deleted_event(): void
    {
        Storage::fake('public');
        $model = TestModel::create();
        $media = $model->addMedia(UploadedFile::fake()->createWithContent('saved.txt', 'saved'))
            ->toMediaCollection('single');
        $component = Livewire::test(MediaItem::class, [
            'model' => $model,
            'mediaCollection' => 'single',
            'inputBaseName' => 'avatar',
            'autoSave' => true,
        ]);

        $component->call('deleteSavedFile');

        $this->assertModelMissing($media);
        $component
            ->assertSet('itemData.id', null)
            ->assertSet('savedFilePath', null)
            ->assertDispatched('luh-media-deleted', inputBaseName: 'avatar', mediaId: $media->id);
    }

    public function test_mount_inherits_accepted_mime_types_and_max_file_size_from_media_collection(): void
    {
        $component = Livewire::test(MediaItem::class, [
            'model' => TestModel::create(),
            'mediaCollection' => 'images',
        ]);

        $component
            ->assertSet('acceptsMimeTypes', ['image/jpeg', 'image/png'])
            ->assertSet('maxFileSize', 1024 * 1024 * 10);
    }

    public function test_unregistered_media_collection_is_rejected(): void
    {
        $thrown = null;

        try {
            Livewire::test(MediaItem::class, [
                'model' => TestModel::create(),
                'mediaCollection' => 'unknown',
            ]);
        } catch (Throwable $throwable) {
            // Livewire may wrap the exception in a ViewException while rendering
            $thrown = $throwable->getPrevious() ?? $throwable;
        }

        $this->assertInstanceOf(MediaCollectionNotRegisteredException::class, $thrown);
    }
}
