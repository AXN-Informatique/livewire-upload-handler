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

    protected function saveFileOrder(string|int $id, int $order): void
    {
        // Save order to database
        DB::table('uploads')
            ->where('id', $id)
            ->update(['order' => $order]);
    }
}
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

### AssetType

```php
use Axn\LivewireUploadHandler\Enums\AssetType;

$type = AssetType::fromFilename('app.js');
$mimeType = $type->withCharset(); // 'application/javascript; charset=utf-8'
```

Types: `JavaScript`, `CSS`

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
- `downloadSavedFile(string)`
- `saveFileOrder(string)`
- `savedFileDisk(string)`
- `savedFilePath(string)`
- `savedFileName(string)`
- `savedFileSize(string)`
- `savedFileMimeType(string)`

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
