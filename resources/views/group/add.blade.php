<label class="{!! $this->cssClasses['add_button'] ?? '' !!}">
    @include('livewire-upload-handler::group.file-input')
    {!! $this->icons['upload'] ?? '' !!}
    {!! __('livewire-upload-handler::actions.add') !!}
</label>
