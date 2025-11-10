<div
    class="luh-errors"
    x-show="Object.keys({!! $errorsVar !!}).length > 0"
    x-cloak
    wire:key="errors"
>
    <template x-for="(error, fileName) in {!! $errorsVar !!}">
        <div class="{!! $this->cssClasses['error'] ?? '' !!}">
            <strong x-text="fileName"></strong>
            <br>
            <span x-text="error"></span>
        </div>
    </template>
</div>
