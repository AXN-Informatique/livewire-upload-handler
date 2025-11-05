<div
    x-show="uploading"
    x-cloak
    wire:key="uploading"
>
    <div x-text="uploadingFileOriginalName"></div>

    <div class="luh__progress">
        <div class="luh__progress-bar">
            @include($this->themedViewPath('progress'))
        </div>
        <div class="luh__progress-cancel">
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
