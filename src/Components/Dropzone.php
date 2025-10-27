<?php

namespace Axn\LivewireUploadHandler\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Dropzone extends Component
{
    public function __construct(
        public bool $disabled = false
    ) {}

    public function render(): View
    {
        return view('livewire-upload-handler::components.dropzone');
    }
}
