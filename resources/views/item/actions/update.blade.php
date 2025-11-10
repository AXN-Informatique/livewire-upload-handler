@if ($this->hasFile)
    <label
        class="{!! $this->cssClasses['update_button'] ?? '' !!}"
        x-show="! deleted"
        wire:key="update"
    >
        @include('livewire-upload-handler::item.file-input')

        {!! $this->icons['upload'] ?? '' !!}
        {!! __('livewire-upload-handler::actions.update') !!}
    </label>
@endif
