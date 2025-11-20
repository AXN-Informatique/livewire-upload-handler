# Basic Usage

## Single File Upload (Item)

```blade
<livewire:upload-handler.item
    wire:model="file"
    :acceptsMimeTypes="['image/jpeg', 'image/png']"
    :maxFileSize="10240"
    :showImagePreview="true"
/>
```

In your Livewire component:

```php
use Livewire\Component;

class MyComponent extends Component
{
    public array $file = [];

    public function save()
    {
        // Access uploaded file via $this->file
    }
}
```

## Multiple Files Upload (Group)

```blade
<livewire:upload-handler.group
    wire:model="files"
    :sortable="true"
    :acceptsMimeTypes="['image/jpeg', 'image/png', 'application/pdf']"
/>
```

In your component:

```php
public array $files = [];
```

## Component Properties

### Common Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `wire:model` | string | required | Livewire model binding |
| `acceptsMimeTypes` | array | `[]` | Allowed MIME types |
| `maxFileSize` | int\|null | `null` | Max size in KB |
| `showImagePreview` | bool | `false` | Show image thumbnails |
| `autoSave` | bool | `false` | Auto-save on upload |
| `onlyUpload` | bool | `false` | Hide file display |
| `compressorjsSettings` | array | `[]` | Compressor.js options |
| `glidePreviewSettings` | array | config | Custom thumbnail size |

### Group-Only Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `sortable` | bool | `false` | Enable drag & drop sorting |
| `inputBaseName` | string | `'files'` | Form input name |

## Auto-Save vs Manual

### Manual Mode (default)

Files stored temporarily until form submission:

```blade
<form wire:submit="save">
    <livewire:upload-handler.item wire:model="file" />
    <button type="submit">Save</button>
</form>
```

```php
public function save()
{
    // Process $this->file
    $path = TemporaryUploadedFile::createFromLivewire($this->file['tmpName'])
        ->store('uploads');
}
```

### Auto-Save Mode

Files saved immediately. See [Media Library Integration](media-library.md).

## Examples

### PDF Upload

```blade
<livewire:upload-handler.item
    wire:model="document"
    :acceptsMimeTypes="['application/pdf']"
    :maxFileSize="20480"
/>
```

### Image with Compression

```blade
<livewire:upload-handler.item
    wire:model="photo"
    :acceptsMimeTypes="['image/jpeg', 'image/png']"
    :showImagePreview="true"
    :compressorjsSettings="[
        'quality' => 0.8,
        'maxWidth' => 1920,
        'maxHeight' => 1080,
    ]"
/>
```

Requires [Compressor.js](https://github.com/fengyuanchen/compressorjs) loaded globally as `window.Compressor`.

### Sortable Gallery

```blade
<livewire:upload-handler.group
    wire:model="gallery"
    :sortable="true"
    :showImagePreview="true"
    :acceptsMimeTypes="['image/jpeg', 'image/png', 'image/webp']"
/>
```

Requires [Sortable.js](https://github.com/SortableJS/Sortable) loaded globally as `window.Sortable`.

## Next Steps

- [Media Library](media-library.md) - Direct integration with Spatie Media Library
- [Events](events.md) - Listen to upload events
- [API Reference](api-reference.md) - All available properties and methods
