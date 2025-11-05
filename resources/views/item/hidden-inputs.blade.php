@if ($this->hasFile && ! $autoSave)
    @if ($uploadedFile !== null)
        <input
            type="hidden"
            name="{!! $this->inputBaseName !!}[tmpName]"
            value="{!! $uploadedFile->getFilename() !!}"
        >
    @elseif ($this->fileId !== null)
        <input
            type="hidden"
            name="{!! $this->inputBaseName !!}[id]"
            value="{!! $this->fileId !!}"
        >
        <input
            type="checkbox"
            name="{!! $this->inputBaseName !!}[deleted]"
            value="1"
            style="display: none"
            wire:model="itemData.deleted"
        >
    @endif
@endif
