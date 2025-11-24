<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire\Concerns;

use Livewire\Attributes\Locked;

use function Axn\LivewireUploadHandler\str_arr_to_dot;

trait Common
{
    #[Locked]
    public array $acceptsMimeTypes = [];

    #[Locked]
    public ?int $maxFileSize = null;

    #[Locked]
    public array $compressorjsSettings = [];

    #[Locked]
    public bool $showFileSize = false;

    #[Locked]
    public bool $showImagePreview = false;

    #[Locked]
    public bool $autoSave = false;

    #[Locked]
    public bool $onlyUpload = false;

    protected function old(): array
    {
        return (array) old(str_arr_to_dot($this->inputBaseName), []);
    }

    protected function initialItemData(array $old = []): array
    {
        return [];
    }
}
