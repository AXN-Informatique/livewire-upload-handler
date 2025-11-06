<div
    @if (! $disabled)
        class="{!! $class !!} luh__dropzone"
        x-init="initDropzone()"
    @endif
>
    @if (! $disabled)
        <div class="{!! $overlayClass !!} luh__dropzone-overlay"></div>
    @endif

    {!! $slot !!}
</div>
