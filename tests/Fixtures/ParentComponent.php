<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Tests\Fixtures;

use Livewire\Component;

class ParentComponent extends Component
{
    public TestModel $model;

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                <livewire:upload-handler.media-item :model="$model" mediaCollection="single" />
                <livewire:upload-handler.media-group :model="$model" mediaCollection="documents" />
            </div>
            BLADE;
    }
}
