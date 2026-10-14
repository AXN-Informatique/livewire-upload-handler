<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Tests\Feature\Livewire;

use Axn\LivewireUploadHandler\Livewire\MediaGroup;
use Axn\LivewireUploadHandler\Tests\Fixtures\TestModel;
use Axn\LivewireUploadHandler\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaGroupTest extends TestCase
{
    public function test_mount_loads_one_item_per_media_in_collection_order(): void
    {
        Storage::fake('public');
        $model = TestModel::create();
        $second = $this->addDocument($model, 'second.txt', order: 2);
        $first = $this->addDocument($model, 'first.txt', order: 1);

        $component = Livewire::test(MediaGroup::class, [
            'model' => $model,
            'mediaCollection' => 'documents',
        ]);

        $this->assertSame([$first->id, $second->id], array_column($component->get('items'), 'id'));
    }

    public function test_renders_saved_media_through_item_components_bound_to_group(): void
    {
        Storage::fake('public');
        $model = TestModel::create();
        $media = $this->addDocument($model, 'first.txt', order: 1);

        $component = Livewire::test(MediaGroup::class, [
            'model' => $model,
            'mediaCollection' => 'documents',
            'inputBaseName' => 'documents',
        ]);

        $itemId = array_key_first($component->get('items'));
        $component
            ->assertSee('first.txt')
            ->assertSeeHtml('name="documents['.$itemId.'][id]"')
            ->assertSeeHtml('value="'.$media->id.'"')
            ->assertSeeHtml('x-modelable="$wire.itemData"');
    }

    public function test_sort_items_with_auto_save_persists_media_order(): void
    {
        Storage::fake('public');
        $model = TestModel::create();
        $first = $this->addDocument($model, 'first.txt', order: 1);
        $second = $this->addDocument($model, 'second.txt', order: 2);
        $component = Livewire::test(MediaGroup::class, [
            'model' => $model,
            'mediaCollection' => 'documents',
            'autoSave' => true,
        ]);
        [$firstItemId, $secondItemId] = array_keys($component->get('items'));

        $component->call('sortItems', [$secondItemId, $firstItemId]);

        $this->assertSame(1, $second->refresh()->order_column);
        $this->assertSame(2, $first->refresh()->order_column);
    }

    public function test_max_files_number_is_capped_by_media_collection_size_limit(): void
    {
        $component = Livewire::test(MediaGroup::class, [
            'model' => TestModel::create(),
            'mediaCollection' => 'limited',
            'maxFilesNumber' => 10,
        ]);

        $component->assertSet('maxFilesNumber', 3);
    }

    private function addDocument(TestModel $model, string $fileName, int $order): Media
    {
        return $model->addMedia(UploadedFile::fake()->createWithContent($fileName, $fileName))
            ->setOrder($order)
            ->toMediaCollection('documents');
    }
}
