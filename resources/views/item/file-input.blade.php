<input
    type="file"
    @if ($acceptsMimeTypes !== [])
        accept="{!! implode(',', $acceptsMimeTypes) !!}"
    @endif
    style="display: none"
    x-on:change="upload($el.files[0])"
>
