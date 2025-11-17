<div
    x-show="maxFilesNumberReached()"
    x-cloak
    wire:key="max-files-number-warning"
>
    <small class="{!! $this->cssClasses['max_files_number_warning'] ?? '' !!}">
        {!! __('livewire-upload-handler::messages.max_files_number_warning') !!}
    </small>
</div>
