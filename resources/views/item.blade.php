<div x-data="LivewireUploadHandlerItem($wire)" class="luh-item">
    @include('livewire-upload-handler::item.hidden-inputs')

    <x-livewire-upload-handler-dropzone
        class="luh-dropzone"
        overlay-class="luh-dropzone-overlay"
        :disabled="$attachedToGroup"
    >
        @if ($this->hasFile)
            <div class="luh-item-content" x-show="! uploading">
                @if ($sortable)
                    <div class="luh-item-sort luh__sort-handle">
                        {!! $this->icons['sort'] ?? '&vellip;' !!}
                    </div>
                @endif

                @if ($this->previewEnabled && $this->fileType->isImage())
                    <div class="luh-item-preview" x-bind:class="{'luh-item-deleted': deleted}">
                        <img src="{{ $this->glideUrl(['w' => 70, 'h' => 70, 'fit' => 'crop']) }}">
                    </div>
                @endif

                <div class="luh-item-body" x-bind:class="{'luh-item-deleted': deleted}">
                    @include('livewire-upload-handler::item.body')
                </div>

                <div class="luh-item-actions">
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
