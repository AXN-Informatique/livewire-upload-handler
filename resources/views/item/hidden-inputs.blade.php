@if ($this->hasFile() && ! $autoSave)
    @if ($this->hasUploadedFile())
        <input
            type="hidden"
            name="{!! $inputBaseName !!}[tmpName]"
            value="{!! $uploadedFile->getFilename() !!}"
        >
    @endif

    @if ($this->hasSavedFile())
        <input
            type="hidden"
            name="{!! $inputBaseName !!}[id]"
            value="{!! $itemData['id'] !!}"
        >

        <input
            type="checkbox"
            name="{!! $inputBaseName !!}[deleted]"
            value="1"
            style="display: none"
            wire:model="itemData.deleted"
        >
    @endif
@endif
