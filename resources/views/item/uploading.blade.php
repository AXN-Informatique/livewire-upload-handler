<div
    x-show="uploading"
    x-cloak
    wire:key="uploading"
>
    <div>
        <span x-text="uploadingFileOriginalName"></span>

        @if ($showFileSize)
            (<span x-text="Math.round($wire.uploadingFileSize / 1024)"></span> KB)
        @endif
    </div>

    <div class="luh-progress">
        <div class="luh-progress-bar">
            @include($this->themedViewPath('progress'))
        </div>
        <div class="luh-progress-cancel">
            <button
                type="button"
                class="{!! $this->cssClasses['cancel_upload_button'] ?? '' !!}"
                x-cloak
                x-on:click="cancelUpload()"
            >
                {!! $this->icons['cancel_upload'] ?? 'X' !!}
            </button>
        </div>
    </div>
</div>
