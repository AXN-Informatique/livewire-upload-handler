<button
    type="button"
    class="{!! $this->cssClasses['cancel_button'] ?? '' !!}"
    x-show="uploading"
    x-cloak
    x-on:click="cancelUpload()"
    wire:key="cancel-upload"
>
    {!! $this->icons['cancel'] !!}
    {!! __('livewire-upload-handler::actions.cancel') !!}
</button>

@if ($hasFile)
    @if (! $attachedToGroup)
        <label
            class="{!! $this->cssClasses['update_button'] ?? '' !!}"
            x-show="! uploading && ! deleted"
            wire:key="update"
        >
            @include('livewire-upload-handler::item.file-input')
            {!! $this->icons['upload'] ?? '' !!}
            {!! __('livewire-upload-handler::actions.update') !!}
        </label>
    @endif

    @if ($uploadedFile !== null)
        <button
            type="button"
            class="{!! $this->cssClasses['cancel_button'] ?? '' !!}"
            x-show="! uploading"
            x-on:click="deleteUploadedFile()"
            wire:key="cancel"
        >
            {!! $this->icons['cancel'] !!}
            {!! __('livewire-upload-handler::actions.cancel') !!}
        </button>
    @else
        <button
            type="button"
            class="{!! $this->cssClasses['delete_button'] ?? '' !!}"
            x-show="! uploading && ! deleted"
            x-on:click="deleteSavedFile()"
            wire:key="delete"
        >
            {!! $this->icons['delete'] ?? __('livewire-upload-handler::actions.delete') !!}
        </button>

        <button
            type="button"
            class="{!! $this->cssClasses['undelete_button'] ?? '' !!}"
            x-show="! uploading && deleted"
            x-cloak
            x-on:click="undeleteSavedFile()"
            wire:key="undelete"
        >
            {!! $this->icons['undelete'] !!}
            {!! __('livewire-upload-handler::actions.undelete') !!}
            @if ($autoSave)
                [ <span x-text="deleteTimer"></span> ]
            @endif
        </button>
    @endif
@endif
