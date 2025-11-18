@if ($this->hasUploadedFile())
    <div
        class="{!! $this->cssClasses['temporary_file_warning'] ?? '' !!}"
        wire:key="temporary-file-warning"
    >
        {!! __('livewire-upload-handler::messages.temporary_file_warning') !!}
    </div>
@endif
