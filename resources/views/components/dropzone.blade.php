<div
    @if (! $disabled)
        class="luh__dropzone"
        x-init="initDropzone()"
    @endif
>
    @if (! $disabled)
        <div class="luh__dropzone-overlay"></div>
    @endif

    {!! $slot !!}
</div>
