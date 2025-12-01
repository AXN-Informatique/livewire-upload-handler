<div
    class="{!! $this->cssClasses['max_files_number_warning'] ?? '' !!}"
    x-show="maxFilesNumberReached()"
    @if (! $this->maxFilesNumberReached()) {{-- To avoid "blip" on page loading --}}
        x-cloak
    @endif
    wire:key="max-files-number-warning"
>
    {!! __('livewire-upload-handler::messages.max_files_number_warning') !!}
</div>
