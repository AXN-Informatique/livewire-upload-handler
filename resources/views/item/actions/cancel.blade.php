@if ($this->hasUploadedFile())
    <button
        type="button"
        class="{!! $this->cssClasses['cancel_button'] ?? '' !!}"
        x-on:click="deleteUploadedFile()"
        wire:key="cancel"
    >
        {!! $this->icons['cancel'] ?? __('livewire-upload-handler::actions.cancel') !!}
    </button>
@endif
