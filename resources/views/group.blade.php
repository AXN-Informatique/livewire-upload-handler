<div x-data="LivewireUploadHandlerGroup($wire)">
    <x-livewire-upload-handler-dropzone
        class="luh-dropzone"
        overlay-class="luh-dropzone-overlay"
    >
        <div x-init="initSortable()">
            @foreach ($items as $itemId => $itemData)
                <div
                    class="luh-group-item luh__sort-draggable"
                    data-id="{!! $itemId !!}"
                    wire:key="{!! $itemId !!}"
                    x-show="! itemHidden('{!! $itemId !!}')"
                >
                    @if ($sortable)
                        <div class="luh-group-item-sort luh__sort-handle">
                            {!! $this->icons['sort'] ?? '&vellip;' !!}
                        </div>
                    @endif

                    @livewire(
                        $this->itemComponentClassName(),
                        $this->itemComponentParams($itemId),
                        key($itemId)
                    )
                </div>
            @endforeach
        </div>

        @include('livewire-upload-handler::group.actions.add')
        @include('livewire-upload-handler::group.warnings.max-files-number-warning')
        @include('livewire-upload-handler::errors', ['errorsVar' => 'groupErrors'])
    </x-livewire-upload-handler-dropzone>
</div>
