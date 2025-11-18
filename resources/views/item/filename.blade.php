<div wire:key="filename">
    @if ($this->fileExists())
        <a
            href=""
            class="{!! $this->cssClasses['download_link'] ?? '' !!}"
            wire:click.prevent="downloadFile()"
        >
            {!! $this->icons['download'] ?? '' !!}
            {{ $this->fileName().($showFileSize ? ' ('.round($this->fileSize() / 1024).' KB)' : '') }}</a>
    @else
        {{ $this->fileName() }}
    @endif
</div>
