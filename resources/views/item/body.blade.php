@if ($this->fileExists)
    <a
        href=""
        class="{!! $this->cssClasses['download_link'] ?? '' !!}"
        wire:click.prevent="downloadFile()"
    >
        {!! $this->icons['download'] ?? '' !!}
        {{ $this->fileName }}</a>
@else
    {{ $this->fileName }}
    <br>
    <small class="{!! $this->cssClasses['missing_file_warning'] ?? '' !!}">
        {!! __('livewire-upload-handler::messages.missing_file_warning') !!}
    </small>
@endif

@if ($uploadedFile !== null)
    <br>
    <small class="{!! $this->cssClasses['temporary_file_warning'] ?? '' !!}">
        {!! __('livewire-upload-handler::messages.temporary_file_warning') !!}
    </small>
@endif
