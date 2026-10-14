<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Tests\Feature\Livewire;

use Axn\LivewireUploadHandler\Exceptions\MethodNotImplementedException;
use Axn\LivewireUploadHandler\Livewire\Group;
use Axn\LivewireUploadHandler\Tests\TestCase;
use Livewire\Livewire;

class GroupTest extends TestCase
{
    public function test_increment_items_adds_new_items_after_existing_ones(): void
    {
        $component = Livewire::test(Group::class);

        $component->call('incrementItems', 2);

        $this->assertSame(
            [
                ['id' => null, 'order' => 1, 'deleted' => false],
                ['id' => null, 'order' => 2, 'deleted' => false],
            ],
            array_values($component->get('items')),
        );
    }

    public function test_increment_items_renders_one_item_component_bound_to_each_new_item(): void
    {
        $component = Livewire::test(Group::class, ['inputBaseName' => 'documents']);

        $component->call('incrementItems', 2);

        foreach (array_keys($component->get('items')) as $itemId) {
            $component->assertSeeHtml('data-id="'.$itemId.'"');
        }
        $this->assertSame(2, substr_count($component->html(), 'x-modelable="$wire.itemData"'));
    }

    public function test_sort_items_reorders_items(): void
    {
        $component = Livewire::test(Group::class)->call('incrementItems', 3);
        [$first, $second, $third] = array_keys($component->get('items'));

        $component->call('sortItems', [$third, $first, $second]);

        $items = $component->get('items');
        $this->assertSame([$third, $first, $second], array_keys($items));
        $this->assertSame([1, 2, 3], array_column($items, 'order'));
    }

    public function test_sort_items_with_auto_save_requires_save_file_order_implementation(): void
    {
        $component = Livewire::test(Group::class, ['autoSave' => true])->call('incrementItems', 1);
        $itemId = array_key_first($component->get('items'));
        $component->set('items.'.$itemId.'.id', 12);

        $this->expectException(MethodNotImplementedException::class);

        $component->call('sortItems', [$itemId]);
    }
}
