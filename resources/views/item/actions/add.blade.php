@if (! $this->hasFile() && ! $attachedToGroup)
    <label
        class="{!! $this->cssClasses['add_button'] ?? '' !!}"
        x-show="! uploading"
        wire:key="add"
    >
        @include('livewire-upload-handler::item.file-input')

        {!! $this->icons['upload'] ?? '' !!}
        {!! __('livewire-upload-handler::actions.add') !!}
    </label>
@endif
