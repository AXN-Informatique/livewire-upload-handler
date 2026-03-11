## livewire-upload-handler

Modern file upload handler for Livewire 3 with chunked uploads, image previews via Glide, and Spatie Media Library integration.

### Livewire Components
- `upload-handler.item` — single file upload with chunking, preview, validation
- `upload-handler.group` — multiple file uploads with sorting (Sortable.js)
- `upload-handler.media-item` — single file with Spatie Media Library auto-save
- `upload-handler.media-group` — multiple files with Media Library integration

### Blade Component

- `@verbatim<x-livewire-upload-handler::dropzone>@endverbatim` — drag-and-drop zone wrapper


### Blade Directives
- `@livewireUploadHandlerScripts` — injects config + compiled JS
- `@livewireUploadHandlerStyles` — injects compiled CSS

### Scaffold Command

@verbatim
<code-snippet name="Generate upload handler component" lang="bash">
php artisan make:upload-handler DocumentUpload
php artisan make:upload-handler DocumentUpload --single
</code-snippet>
@endverbatim

- IMPORTANT: Activate `upload-handler` skill for detailed usage patterns, component properties, and extension examples.
