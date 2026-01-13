---
title: Advanced Usage
order: 7
---

# Advanced Usage

If you need more control on upload handler component, you can create custom upload handlers by extending components.

## Custom Handler

Use command `php artisan make:upload-handler`

Arguments:
- `name`: The name of the upload handler component.

Options:
- `--single`: Generate component for single upload only
- `--force`: Overwrite existing files

### Examples

`php artisan make:upload-handler MyUploadHandler`

Generated files:

```
app/
    Livewire/
        MyUploadHandler/
            Concerns/
                MyUploadHandlerCommon.php
            Group.php
            Item.php
resources/
    views/
        livewire/
            my-upload-handler/
                group.blade.php
                item.blade.php
```

`php artisan make:upload-handler MyUploadHandler --single`

```
app/
    Livewire/
        MyUploadHandler.php
resources/
    views/
        livewire/
            my-upload-handler.blade.php
```

## Enums

### FileType

```php
use Axn\LivewireUploadHandler\Enums\FileType;

$type = FileType::fromMimeType('image/jpeg');

if ($type->isImage()) {
    // Handle image
}

if ($type->supportsPreview()) {
    // Generate preview
}
```

Available types: `Image`, `Video`, `Audio`, `Document`, `Archive`, `Other`

Methods:
- `fromMimeType(string): FileType`
- `isImage(): bool`
- `isVideo(): bool`
- `isAudio(): bool`
- `isDocument(): bool`
- `isArchive(): bool`
- `supportsPreview(): bool`

## Exceptions

### MethodNotImplementedException

Thrown when required methods not implemented in custom handlers.

```php
use Axn\LivewireUploadHandler\Exceptions\MethodNotImplementedException;

protected function saveUploadedFile(TemporaryUploadedFile $file): void
{
    throw MethodNotImplementedException::saveUploadedFile(static::class);
}
```

Factory methods:
- `saveUploadedFile(string)`
- `deleteSavedFile(string)`
- `saveFileOrder(string)`

Error message guides you to implement the method or use MediaItem/MediaGroup.

## JavaScript Integration

### Compressor.js

Load before `@livewireUploadHandlerScripts`:

```html
<script src="https://cdn.jsdelivr.net/npm/compressorjs@latest/dist/compressor.min.js"></script>
<script>window.Compressor = Compressor;</script>
@livewireUploadHandlerScripts
```

Configure per component:

```blade
<livewire:upload-handler.item
    :compressorjsSettings="['quality' => 0.8, 'maxWidth' => 1920]"
/>
```

### Sortable.js

Load before `@livewireUploadHandlerScripts`:

```html
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>window.Sortable = Sortable;</script>
@livewireUploadHandlerScripts
```

Enable on Group:

```blade
<livewire:upload-handler.group :sortable="true" />
```

## Next Steps

- [Troubleshooting](troubleshooting.md) - Common issues
