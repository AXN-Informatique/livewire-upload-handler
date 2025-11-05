<div x-data="LivewireUploadHandlerGroup($wire)">
    <x-livewire-upload-handler-dropzone>
        <div x-init="initSortable()">
            @foreach ($items as $itemId => $itemData)
                <div
                    class="luh__group-item js--luh__sort-draggable"
                    data-id="{!! $itemId !!}"
                    wire:key="{!! $itemId !!}"
                    x-show="! itemHidden('{!! $itemId !!}')"
                >
                    @livewire(
                        $this->itemComponentClassName(),
                        $this->itemComponentParams($itemId),
                        key($itemId)
                    )
                </div>
            @endforeach
        </div>

        @include('livewire-upload-handler::group.actions.add')
        @include('livewire-upload-handler::errors')
    </x-livewire-upload-handler-dropzone>
</div>
