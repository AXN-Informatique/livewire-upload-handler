<?php

namespace Axn\LivewireUploadHandler\Livewire;

use Axn\LivewireUploadHandler\Livewire\Concerns\HasThemes;
use Exception;
use function Axn\LivewireUploadHandler\str_arr_to_dot;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;

use Livewire\Attributes\Renderless;
use Livewire\Component;

class Group extends Component
{
    use HasThemes;

    #[Modelable]
    public array $items = [];

    #[Locked]
    public string $inputBaseName = 'files';

    #[Locked]
    public ?array $acceptsMimeTypes = null;

    #[Locked]
    public ?int $maxFileSize = null;

    #[Locked]
    public ?array $compressorjsSettings = null;

    #[Locked]
    public ?array $glidePreviewSettings = null;

    #[Locked]
    public ?bool $previewImage = null;

    #[Locked]
    public ?bool $autoSave = null;

    #[Locked]
    public ?bool $onlyUpload = null;

    #[Locked]
    public bool $sortable = false;

    protected array $uploadFromGroupAtIndex = [];

    public function mount()
    {
        $this->acceptsMimeTypes ??= $this->propertyValueFromItem('acceptsMimeTypes');
        $this->maxFileSize ??= $this->propertyValueFromItem('maxFileSize');
        $this->compressorjsSettings ??= $this->propertyValueFromItem('compressorjsSettings');
        $this->glidePreviewSettings ??= $this->propertyValueFromItem('glidePreviewSettings');
        $this->previewImage ??= $this->propertyValueFromItem('previewImage');
        $this->autoSave ??= $this->propertyValueFromItem('autoSave');
        $this->onlyUpload ??= $this->propertyValueFromItem('onlyUpload');

        if (old() !== []) {
            $order = 1;

            foreach (old(str_arr_to_dot($this->inputBaseName), []) as $itemId => $oldData) {
                $this->items[$itemId] = [
                    'id' => $oldData['id'] ?? null,
                    'order' => $order++,
                    'deleted' => ! empty($oldData['deleted']),
                ];
            }
        }
    }

    public function incrementItems(int $count): void
    {
        for ($index = 0; $index < $count; $index++) {
            $itemId = $this->addItem();

            $this->uploadFromGroupAtIndex[$itemId] = $index;
        }
    }

    protected function addItem(array $data = []): string
    {
        $data['id'] ??= null;
        $data['order'] ??= collect($this->items)->max('order') + 1;
        $data['deleted'] ??= false;

        $this->items[$id = uniqid()] = $data;

        return $id;
    }

    #[Renderless]
    public function sortItems(array $sortedItemsIds): void
    {
        foreach ($sortedItemsIds as $order => $itemId) {
            $this->items[$itemId]['order'] = ++$order;

            if ($this->autoSave) {
                $this->saveItemOrder($itemId, $order);
            }
        }

        uasort($this->items, fn ($a, $b) => $a['order'] <=> $b['order']);
    }

    protected function saveItemOrder(string $itemId, int $order): void
    {
        throw new Exception('`saveItemOrder` not handled by this component.');
    }

    public function render()
    {
        return view($this->viewName());
    }

    protected function viewName(): string
    {
        return 'livewire-upload-handler::group';
    }

    protected function itemComponentClassName(): string
    {
        return Item::class;
    }

    protected function itemComponentParams(string $itemId): array
    {
        return [
            'itemId' => $itemId,
            'wire:model' => 'items.'.$itemId,
            'inputBaseName' => $this->inputBaseName.'['.$itemId.']',
            'acceptsMimeTypes' => $this->acceptsMimeTypes,
            'maxFileSize' => $this->maxFileSize,
            'compressorjsSettings' => $this->compressorjsSettings,
            'glidePreviewSettings' => $this->glidePreviewSettings,
            'previewImage' => $this->previewImage,
            'autoSave' => $this->autoSave,
            'onlyUpload' => $this->onlyUpload,
            'sortable' => $this->sortable,
            'attachedToGroup' => true,
            'uploadFromGroupAtIndex' => $this->uploadFromGroupAtIndex[$itemId] ?? null,
        ];
    }

    protected function propertyValueFromItem($property)
    {
        return app($this->itemComponentClassName())->$property;
    }
}
