@if ($this->hasFile && $uploadedFile === null)
    <button
        type="button"
        class="{!! $this->cssClasses['undelete_button'] ?? '' !!}"
        x-show="deleted"
        x-cloak
        x-on:click="undeleteSavedFile()"
        wire:key="undelete"
    >
        {!! $this->icons['undelete'] !!}
        {!! __('livewire-upload-handler::actions.undelete') !!}

        @if ($autoSave)
            (<strong x-text="deleteTimer"></strong>s.)
        @endif
    </button>
@endif
