@if (! $this->hasFile() && ! $attachedToGroup)
    <label
        class="{!! $this->cssClasses['add_button'] ?? '' !!}"
        wire:key="add"
    >
        @include('livewire-upload-handler::item.file-input')

        {!! $this->icons['add'] ?? '' !!}
        {!! __('livewire-upload-handler::actions.add') !!}
    </label>
@endif
