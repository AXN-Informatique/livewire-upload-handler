<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire;

use Axn\LivewireUploadHandler\Exceptions\MethodNotImplementedException;
use Axn\LivewireUploadHandler\Livewire\Concerns\HasThemes;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\Renderless;
use Livewire\Component;

use function Axn\LivewireUploadHandler\str_arr_to_dot;

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
    public ?bool $previewEnabled = null;

    #[Locked]
    public ?bool $autoSave = null;

    #[Locked]
    public ?bool $onlyUpload = null;

    #[Locked]
    public bool $sortable = false;

    protected array $uploadFromGroupAtIndex = [];

    public function mount(): void
    {
        $this->acceptsMimeTypes ??= $this->propertyValueFromItem('acceptsMimeTypes');
        $this->maxFileSize ??= $this->propertyValueFromItem('maxFileSize');
        $this->compressorjsSettings ??= $this->propertyValueFromItem('compressorjsSettings');
        $this->previewEnabled ??= $this->propertyValueFromItem('previewEnabled');
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

    /**
     * Increment items count for batch upload.
     */
    public function incrementItems(int $count): void
    {
        for ($index = 0; $index < $count; $index++) {
            $itemId = $this->addItem();

            $this->uploadFromGroupAtIndex[$itemId] = $index;
        }
    }

    /**
     * Add a new item to the group.
     *
     * @param  array{id?: int|string|null, order?: int, deleted?: bool}  $data
     */
    protected function addItem(array $data = []): string
    {
        $data['id'] ??= null;
        $data['order'] ??= collect($this->items)->max('order') + 1;
        $data['deleted'] ??= false;

        $this->items[$id = uniqid()] = $data;

        return $id;
    }

    /**
     * Sort items in the group.
     */
    #[Renderless]
    public function sortItems(array $sortedItemsIds): void
    {
        foreach ($sortedItemsIds as $order => $itemId) {
            $this->items[$itemId]['order'] = ++$order;

            if ($this->autoSave) {
                $this->saveItemOrder($itemId, $order);
            }
        }

        uasort($this->items, fn (array $a, array $b): int => $a['order'] <=> $b['order']);
    }

    /**
     * Save item order to permanent storage.
     * Must be implemented in child classes when using autoSave with sortable.
     *
     * @throws MethodNotImplementedException
     */
    protected function saveItemOrder(string $itemId, int $order): void
    {
        throw MethodNotImplementedException::saveItemOrder(static::class);
    }

    public function render(): View
    {
        return view('livewire-upload-handler::group');
    }

    /**
     * Get the item component class name.
     */
    protected function itemComponentClassName(): string
    {
        return Item::class;
    }

    /**
     * Get parameters to pass to item components.
     *
     * @return array<string, mixed>
     */
    protected function itemComponentParams(string $itemId): array
    {
        return [
            'itemId' => $itemId,
            'wire:model' => 'items.'.$itemId,
            'inputBaseName' => $this->inputBaseName.'['.$itemId.']',
            'acceptsMimeTypes' => $this->acceptsMimeTypes,
            'maxFileSize' => $this->maxFileSize,
            'compressorjsSettings' => $this->compressorjsSettings,
            'previewEnabled' => $this->previewEnabled,
            'autoSave' => $this->autoSave,
            'onlyUpload' => $this->onlyUpload,
            'sortable' => $this->sortable,
            'attachedToGroup' => true,
            'uploadFromGroupAtIndex' => $this->uploadFromGroupAtIndex[$itemId] ?? null,
        ];
    }

    /**
     * Get property value from item component instance.
     */
    protected function propertyValueFromItem(string $property): mixed
    {
        return app($this->itemComponentClassName())->$property;
    }
}
