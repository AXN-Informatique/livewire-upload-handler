<div x-data="LivewireUploadHandlerItem($wire)" class="luh__item">
    @include('livewire-upload-handler::item.hidden-inputs')

    <x-livewire-upload-handler-dropzone :disabled="$attachedToGroup">
        @if ($this->hasFile)
            <div class="luh__item-content" x-show="! uploading">
                @if ($sortable)
                    <div class="luh__item-sort js--luh__sort-handle">
                        {!! $this->icons['sort'] ?? '&vellip;' !!}
                    </div>
                @endif

                @if ($this->imagePreviewUrl !== null)
                    <div class="luh__item-preview" x-bind:class="{'luh__item--deleted': deleted}">
                        <img src="{{ $this->imagePreviewUrl }}">
                    </div>
                @endif

                <div class="luh__item-body" x-bind:class="{'luh__item--deleted': deleted}">
                    @include('livewire-upload-handler::item.body')
                </div>

                <div class="luh__item-actions">
                    @include('livewire-upload-handler::item.actions.update')
                    @include('livewire-upload-handler::item.actions.delete')
                    @include('livewire-upload-handler::item.actions.undelete')
                    @include('livewire-upload-handler::item.actions.cancel')
                </div>
            </div>
        @endif

        @include('livewire-upload-handler::item.actions.add')
        @include('livewire-upload-handler::item.uploading')

        @if (! $attachedToGroup)
            @include('livewire-upload-handler::errors')
        @endif
    </x-livewire-upload-handler-dropzone>
</div>
