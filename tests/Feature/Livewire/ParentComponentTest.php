<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Tests\Feature\Livewire;

use Axn\LivewireUploadHandler\Tests\Fixtures\ParentComponent;
use Axn\LivewireUploadHandler\Tests\Fixtures\TestModel;
use Axn\LivewireUploadHandler\Tests\TestCase;
use Livewire\Livewire;
use Orchestra\Testbench\Attributes\WithConfig;

class ParentComponentTest extends TestCase
{
    #[WithConfig('livewire.smart_wire_keys', true, defer: false)]
    public function test_parent_update_does_not_remount_components_rendered_after_an_upload_handler(): void
    {
        // Views compiled by a previous run without smart wire keys would hide the issue
        $this->artisan('view:clear');

        $component = Livewire::test(ParentComponent::class, ['model' => TestModel::create()]);
        $childrenOnMount = $component->snapshot['memo']['children'];

        $component->call('$refresh');

        $this->assertSame($childrenOnMount, $component->snapshot['memo']['children']);
    }
}
