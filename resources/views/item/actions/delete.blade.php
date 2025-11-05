@if ($this->hasFile && $uploadedFile === null)
    <button
        type="button"
        class="{!! $this->cssClasses['delete_button'] ?? '' !!}"
        x-show="! deleted"
        x-on:click="deleteSavedFile()"
        wire:key="delete"
    >
        {!! $this->icons['delete'] ?? __('livewire-upload-handler::actions.delete') !!}
    </button>
@endif
