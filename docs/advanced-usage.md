# Advanced Usage

## Custom Upload Handler

Extend `Item` for custom storage:

```php
namespace App\Livewire;

use Axn\LivewireUploadHandler\Livewire\Item;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class S3UploadItem extends Item
{
    protected function saveUploadedFile(TemporaryUploadedFile $file): void
    {
        $path = $file->storeAs('uploads', $file->getClientOriginalName(), 's3');

        $this->itemData['path'] = $path;
        $this->dispatch('file-saved', path: $path);
    }

    protected function savedFileName(): ?string
    {
        return $this->itemData['filename'] ?? null;
    }
}
```

Use it:

```blade
<livewire:s3-upload-item wire:model="file" :autoSave="true" />
```

## Custom Group Handler

```php
namespace App\Livewire;

use Axn\LivewireUploadHandler\Livewire\Group;

class CustomGroup extends Group
{
    protected function itemComponentClassName(): string
    {
        return S3UploadItem::class;
    }

    protected function saveItemOrder(string $itemId, int $order): void
    {
        // Save order to database
        DB::table('uploads')
            ->where('id', $this->items[$itemId]['id'])
            ->update(['order' => $order]);
    }
}
```

## Enums

### MediaType

```php
use Axn\LivewireUploadHandler\Enums\MediaType;

$type = MediaType::fromMimeType('image/jpeg');

if ($type->isImage()) {
    // Handle image
}

if ($type->supportsPreview()) {
    // Generate preview
}
```

Available types: `Image`, `Video`, `Audio`, `Document`, `Archive`, `Other`

Methods:
- `fromMimeType(string): MediaType`
- `isImage(): bool`
- `isVideo(): bool`
- `isAudio(): bool`
- `isDocument(): bool`
- `isArchive(): bool`
- `supportsPreview(): bool`

### FileState

```php
use Axn\LivewireUploadHandler\Enums\FileState;

$state = FileState::Uploading;

if ($state->isUploading()) {
    // Show progress
}

if ($state->isComplete()) {
    // File ready
}
```

States: `Uploading`, `Uploaded`, `Saved`, `Error`, `Deleted`

### AssetType

```php
use Axn\LivewireUploadHandler\Enums\AssetType;

$type = AssetType::fromFilename('app.js');
$mimeType = $type->withCharset(); // 'application/javascript; charset=utf-8'
```

Types: `JavaScript`, `CSS`

## Exceptions

### UploadException

```php
use Axn\LivewireUploadHandler\Exceptions\UploadException;

try {
    // Upload code
} catch (UploadException $e) {
    logger()->error('Upload failed', ['exception' => $e]);
}
```

Factory methods:
- `chunkProcessingFailed(Throwable)`
- `validationFailed(string)`
- `fileNotFound(string)`
- `invalidMimeType(string, array)`
- `fileTooLarge(int, int)`

### FileNotHandledException

Thrown when required methods not implemented in custom handlers.

```php
use Axn\LivewireUploadHandler\Exceptions\FileNotHandledException;

protected function saveUploadedFile(TemporaryUploadedFile $file): void
{
    throw FileNotHandledException::saveUploadedFile(static::class);
}
```

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

- [API Reference](api-reference.md) - Complete method reference
- [Troubleshooting](troubleshooting.md) - Common issues
