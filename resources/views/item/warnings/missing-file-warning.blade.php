@if ($this->hasFile() && ! $this->fileExists())
    <div
        class="{!! $this->cssClasses['missing_file_warning'] ?? '' !!}"
        wire:key="missing-file-warning"
    >
        {!! __('livewire-upload-handler::messages.missing_file_warning') !!}
    </div>
@endif
