@if ($this->hasFile())
    <label
        class="{!! $this->cssClasses['replace_button'] ?? '' !!}"
        x-show="! deleted"
        wire:key="replace"
    >
        @include('livewire-upload-handler::item.file-input')

        {!! $this->icons['replace'] ?? '' !!}
        {!! __('livewire-upload-handler::actions.replace') !!}
    </label>
@endif
