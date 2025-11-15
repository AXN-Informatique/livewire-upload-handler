<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire;

use Axn\LivewireUploadHandler\Exceptions\MethodNotImplementedException;
use Axn\LivewireUploadHandler\Livewire\Concerns\Common;
use Axn\LivewireUploadHandler\Livewire\Concerns\HasThemes;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use ReflectionClass;
use ReflectionProperty;

class Group extends Component
{
    use Common, HasThemes;

    #[Modelable]
    public array $items = [];

    #[Locked]
    public string $inputBaseName = 'files';

    #[Locked]
    public bool $sortable = false;

    protected array $uploadFromGroupAtIndex = [];

    public function mount(): void
    {
        $this->loadInitialItemsData();
    }

    protected function loadInitialItemsData(): void
    {
        if ($this->onlyUpload) {
            return;
        }

        $order = 1;

        foreach ($this->old() as $itemId => $old) {
            $this->items[$itemId] = [
                'id' => $old['id'] ?? null,
                'order' => $order++,
                'deleted' => ! empty($old['deleted']),
                ...$this->initialItemData($old),
            ];
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

        $this->items[$id = uniqid('_')] = $data;

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
            'attachedToGroup' => true,
            'uploadFromGroupAtIndex' => $this->uploadFromGroupAtIndex[$itemId] ?? null,
            ...$this->publicPropsFrom(Common::class),
        ];
    }

    /**
     * Used to extract public properties values of a common trait and pass them
     * to item components (see method itemComponentParams).
     */
    protected function publicPropsFrom(string $trait): array
    {
        $props = (new ReflectionClass($trait))
            ->getProperties(ReflectionProperty::IS_PUBLIC);

        return collect($props)
            ->mapWithKeys(fn (ReflectionProperty $prop): array => [
                $prop->getName() => $this->{$prop->getName()},
            ])
            ->all();
    }
}
