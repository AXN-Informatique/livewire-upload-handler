<label
    class="{!! $this->cssClasses['add_button'] ?? '' !!}"
    x-show="! maxFilesNumberReached()"
    @if ($this->maxFilesNumberReached()) {{-- To avoid "blip" on page loading --}}
        x-cloak
    @endif
    wire:key="add"
>
    @include('livewire-upload-handler::group.file-input')

    {!! $this->icons['add'] ?? '' !!}
    {!! __('livewire-upload-handler::actions.add') !!}
</label>
