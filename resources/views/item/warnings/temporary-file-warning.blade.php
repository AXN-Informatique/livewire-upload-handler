@if ($uploadedFile !== null)
    <div wire:key="temporary-file-warning">
        <small class="{!! $this->cssClasses['temporary_file_warning'] ?? '' !!}">
            {!! __('livewire-upload-handler::messages.temporary_file_warning') !!}
        </small>
    </div>
@endif
