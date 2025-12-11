@if ($this->hasUploadedFile())
    <input
        type="hidden"
        name="{!! $inputBaseName !!}[tmpName]"
        value="{!! $uploadedFile->getFilename() !!}"
    >
@endif

@if ($this->hasSavedFile())
    @isset($itemData['id'])
        <input
            type="hidden"
            name="{!! $inputBaseName !!}[id]"
            value="{!! $itemData['id'] !!}"
        >
    @endisset

    @if (! $autoSave)
        <input
            type="checkbox"
            name="{!! $inputBaseName !!}[deleted]"
            value="1"
            style="display: none"
            wire:model="itemData.deleted"
        >
    @endif
@endif
