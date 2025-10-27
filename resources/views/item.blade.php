<div x-data="LivewireUploadHandlerItem($wire)" class="luh__item">
    @include('livewire-upload-handler::item.hidden-inputs')

    <x-livewire-upload-handler-dropzone :disabled="$attachedToGroup">
        <div
            class="luh__item-content"
            x-show="uploading || $wire.hasFile"
            @if (! $hasFile) x-cloak @endif
        >
            @if ($sortable)
                <div class="luh__item-sort js--luh__sort-handle">
                    {!! $this->icons['sort'] ?? '&vellip;' !!}
                </div>
            @endif

            @if ($this->imagePreviewUrl !== null)
                <div
                    class="luh__item-preview"
                    x-bind:class="{'luh__item--deleted': deleted}"
                >
                    <img src="{{ $this->imagePreviewUrl }}">
                </div>
            @endif

            <div class="luh__item-body">
                @include('livewire-upload-handler::item.progress')

                @if ($hasFile)
                    <div
                        x-show="! uploading"
                        x-bind:class="{'luh__item--deleted': deleted}"
                    >
                        @include('livewire-upload-handler::item.body')
                    </div>
                @endif
            </div>

            <div class="luh__item-actions">
                @include('livewire-upload-handler::item.actions')
            </div>
        </div>

        @include('livewire-upload-handler::item.add')

        @if (! $attachedToGroup)
            @include('livewire-upload-handler::errors')
        @endif
    </x-livewire-upload-handler-dropzone>
</div>
