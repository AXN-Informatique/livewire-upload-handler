<label
    class="{!! $this->cssClasses['add_button'] ?? '' !!}"
    x-show="! maxFilesNumberReached()"
    x-cloak
    wire:key="add"
>
    @include('livewire-upload-handler::group.file-input')

    {!! $this->icons['upload'] ?? '' !!}
    {!! __('livewire-upload-handler::actions.add') !!}
</label>
