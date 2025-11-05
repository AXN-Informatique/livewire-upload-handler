@if ($this->hasFile && ! $this->fileExists)
    <div wire:key="missing-file-warning">
        <small class="{!! $this->cssClasses['missing_file_warning'] ?? '' !!}">
            {!! __('livewire-upload-handler::messages.missing_file_warning') !!}
        </small>
    </div>
@endif
