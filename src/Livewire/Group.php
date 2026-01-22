<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire;

use Axn\LivewireUploadHandler\Exceptions\MethodNotImplementedException;
use Axn\LivewireUploadHandler\Livewire\Concerns\Common;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use ReflectionClass;
use ReflectionProperty;

class Group extends Component
{
    use Common;

    #[Modelable]
    public ?array $items = [];

    #[Locked]
    public string $inputBaseName = 'files';

    #[Locked]
    public int $maxFilesNumber = 0;

    #[Locked]
    public bool $sortable = false;

    protected array $itemsParams = [];

    public function mount(): void
    {
        if ($this->onlyUpload) {
            return;
        }

        $this->initItems();
    }

    protected function initialEntities(): array|Collection
    {
        return [];
    }

    protected function initItems(): void
    {
        $entities = $this->initialEntities();

        $this->items = [];

        if (old() !== []) {
            foreach ($this->old() as $itemId => $old) {
                $entity = isset($old['id']) && isset($entities[$old['id']])
                    ? $entities[$old['id']]
                    : null;

                $this->addItem(
                    $itemId,
                    $this->initialItemData($old, $entity),
                    $this->initialItemParams($entity),
                );
            }
        } else {
            foreach ($entities as $entity) {
                $this->addItem(
                    null,
                    $this->initialItemData([], $entity),
                    $this->initialItemParams($entity),
                );
            }
        }
    }

    #[On('livewire-upload-handler:refresh')]
    public function refreshItems(?string $inputBaseName = null): void
    {
        if ($inputBaseName !== null && $inputBaseName !== $this->inputBaseName) {
            return;
        }

        $this->initItems();
    }

    /**
     * Increment items count for batch upload.
     */
    public function incrementItems(int $count): void
    {
        for ($index = 0; $index < $count; $index++) {
            $this->addItem(null, [], ['uploadFromGroupAtIndex' => $index]);
        }
    }

    /**
     * Add a new item to the group.
     *
     * @param  array{id?: int|string|null, order?: int, deleted?: bool}  $data
     */
    protected function addItem(?string $itemId = null, array $data = [], array $params = []): string
    {
        $itemId ??= uniqid('_');

        $data['id'] ??= null;
        $data['order'] ??= collect($this->items)->max('order') + 1;
        $data['deleted'] ??= false;

        $this->items[$itemId] = $data;

        $this->itemsParams[$itemId] = [
            ...$this->commonParams,
            ...$params,
            'itemId' => $itemId,
            'wire:model' => 'items.'.$itemId,
            'inputBaseName' => $this->inputBaseName.'['.$itemId.']',
            'attachedToGroup' => true,
        ];

        return $itemId;
    }

    /**
     * Sort items in the group.
     */
    #[Renderless]
    public function sortItems(array $sortedItemsIds): void
    {
        foreach ($sortedItemsIds as $order => $itemId) {
            $this->items[$itemId]['order'] = ++$order;
            $id = $this->items[$itemId]['id'] ?? null;

            if ($this->autoSave && $id !== null) {
                $this->saveFileOrder($id, $order);
            }
        }

        uasort($this->items, fn (array $a, array $b): int => $a['order'] <=> $b['order']);
    }

    /**
     * Save file order to permanent storage.
     * Must be implemented in child classes when using autoSave with sortable.
     *
     * @throws MethodNotImplementedException
     */
    protected function saveFileOrder(string|int $id, int $order): void
    {
        throw MethodNotImplementedException::saveFileOrder(static::class);
    }

    public function render(): View
    {
        return view('livewire-upload-handler::group');
    }

    protected function maxFilesNumberReached(): bool
    {
        return $this->maxFilesNumber > 0 && \count($this->items) >= $this->maxFilesNumber;
    }

    /**
     * Get the item component class name.
     * Called in group view to load the corresponding item component.
     */
    protected function itemComponentClassName(): string
    {
        return Item::class;
    }

    /**
     * References Common Traits for extracting common params (public properties of the Traits)
     * to pass them from group to item.
     *
     * @return array<string>
     */
    protected function commonTraits(): array
    {
        return [
            Common::class,
        ];
    }

    /**
     * Extract common params from public properties defined in Common Traits.
     */
    #[Computed]
    protected function commonParams(): array
    {
        $params = [];

        foreach ($this->commonTraits() as $trait) {
            $props = new ReflectionClass($trait)
                ->getProperties(ReflectionProperty::IS_PUBLIC);

            foreach ($props as $prop) {
                if (! array_key_exists($prop->getName(), $params)) {
                    $params[$prop->getName()] = $this->{$prop->getName()};
                }
            }
        }

        return $params;
    }
}
