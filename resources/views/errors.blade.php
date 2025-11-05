<div
    class="luh__errors"
    x-show="Object.keys(errors).length > 0"
    x-cloak
    wire:key="errors"
>
    <template x-for="(error, fileName) in errors">
        <div class="{!! $this->cssClasses['error'] ?? '' !!}">
            <strong x-text="fileName"></strong>
            <br>
            <span x-text="error"></span>
        </div>
    </template>
</div>
