@php
/** @var \Laravel\Boost\Install\GuidelineAssist $assist */
@endphp
# Livewire Upload Handler

- Modern file upload handler for Livewire 3 with chunked uploads, image previews via Glide, and Spatie Media Library integration.
- Livewire components: `upload-handler.item` (single), `upload-handler.group` (multiple), `upload-handler.media-item` and `upload-handler.media-group` (Media Library).
- Blade component: `x-livewire-upload-handler-dropzone` for drag-and-drop zone wrapper.
- Blade directives: `@livewireUploadHandlerScripts` (config + JS) and `@livewireUploadHandlerStyles` (CSS).
- Scaffold: `{{ $assist->artisanCommand('make:upload-handler DocumentUpload') }}` (or `--single` for single item).
- IMPORTANT: Activate `upload-handler` skill for detailed usage patterns, component properties, events, and extension examples.
