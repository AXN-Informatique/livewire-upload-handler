<div wire:key="filename">
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
    @endif
</div>

@include('livewire-upload-handler::item.warnings.missing-file-warning')
@include('livewire-upload-handler::item.warnings.temporary-file-warning')
