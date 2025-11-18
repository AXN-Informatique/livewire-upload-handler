<div
    class="{!! $this->cssClasses['max_files_number_warning'] ?? '' !!}"
    x-show="maxFilesNumberReached()"
    x-cloak
    wire:key="max-files-number-warning"
>
    {!! __('livewire-upload-handler::messages.max_files_number_warning') !!}
</div>
