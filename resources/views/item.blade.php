<div x-data="LivewireUploadHandlerItem($wire)" class="luh-item">
    @include('livewire-upload-handler::item.hidden-inputs')

    <x-livewire-upload-handler-dropzone
        class="luh-dropzone"
        overlay-class="luh-dropzone-overlay"
        :disabled="$attachedToGroup"
    >
        <div x-show="! uploading">
            @if ($this->hasFile())
                <div class="luh-item-content" wire:key="content">
                    @if ($showImagePreview && $this->fileExists() && $this->fileType()->isImage())
                        <div class="luh-item-preview" x-bind:class="{'luh-item-deleted': deleted}">
                            <img src="{{ $this->glideUrl(['w' => 70, 'h' => 70, 'fit' => 'crop']) }}">
                        </div>
                    @endif

                    <div class="luh-item-body" x-bind:class="{'luh-item-deleted': deleted}" wire:key="body">
                        @include('livewire-upload-handler::item.filename')
                        @include('livewire-upload-handler::item.warnings.missing-file-warning')
                        @include('livewire-upload-handler::item.warnings.temporary-file-warning')

                        {{-- <input
                            type="text"
                            name="{!! $inputBaseName !!}[name]"
                            id="{!! $itemId !!}_name"
                            wire:model="itemData.name"
                        > --}}
                    </div>

                    <div class="luh-item-actions" wire:key="actions">
                        <div class="{!! $this->cssClasses['actions_group'] ?? '' !!}">
                            @include('livewire-upload-handler::item.actions.replace')
                            @include('livewire-upload-handler::item.actions.delete')
                            @include('livewire-upload-handler::item.actions.cancel')
                        </div>

                        @include('livewire-upload-handler::item.actions.undelete')
                    </div>
                </div>
            @endif

            @include('livewire-upload-handler::item.actions.add')
        </div>

        <div x-show="uploading" x-cloak>
            @include('livewire-upload-handler::item.uploading')
        </div>

        @include('livewire-upload-handler::errors', ['errorsVar' => 'itemErrors'])
    </x-livewire-upload-handler-dropzone>
</div>
