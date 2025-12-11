# Basic Usage

## Single File Upload (Item)

```blade
<livewire:upload-handler.item
    :acceptsMimeTypes="['image/jpeg', 'image/png']"
    :maxFileSize="10240"
    :showFileSize="true"
    :showImagePreview="true"
/>
```

## Multiple Files Upload (Group)

```blade
<livewire:upload-handler.group
    :acceptsMimeTypes="['image/jpeg', 'image/png', 'application/pdf']"
    :maxFilesNumber="5"
    :sortable="true"
/>
```

## Component Properties

### Common Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `acceptsMimeTypes` | array | `[]` | Allowed MIME types |
| `maxFileSize` | int | `0` | Max size in KB (0 = no limit) |
| `showFileSize` | bool | `false` | Show file size in KB |
| `showImagePreview` | bool | `false` | Show image thumbnails |
| `autoSave` | bool | `false` | Auto-save on upload |
| `onlyUpload` | bool | `false` | Hide file display |
| `compressorjsSettings` | array | `[]` | Compressor.js options |
| `savedFileDisk` | string | `'local'` | Saved files disk name |
| `theme` | string\|null | `null` | CSS and views theme. If null, use `config('livewire-upload-handler.theme') ?: 'default'` |
| `iconsTheme` | string\|null | `null` | Icons theme. If null, use `config('livewire-upload-handler.icons_theme')` |

### Group-Only Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `inputBaseName` | string | `'files'` | Form input base name |
| `maxFilesNumber` | int | `0` | Maximum number of files (0 = no limit) |
| `sortable` | bool | `false` | Enable drag & drop sorting |

### Item-Only Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `inputBaseName` | string | `'file'` | Form input base name |
| `savedFilePath` | string\|null | `null` | Saved file path relative to disk |

## Auto-Save vs Manual

### Manual Mode (default)

Files stored temporarily until form submission:

```blade
<form action="{!! route('files.store') !!}" method="POST">
    @csrf

    {{-- ===== Single ===== --}}
    <livewire:upload-handler.item />

    {{-- ===== Multiple ===== --}}
    <livewire:upload-handler.group />

    <button type="submit">Save</button>
</form>
```

```php
public function store(Request $request)
{
    // ===== Single =====
    $uploadedFile = TemporaryUploadedFile::createFromLivewire($request->input('file.tmpName'));
    // Handle $uploadedFile

    // ===== Multiple =====
    foreach ($request->post('files') as $data) {
        $uploadedFile = TemporaryUploadedFile::createFromLivewire($data['tmpName']);
        // Handle $uploadedFile
    }
}
```

### Auto-Save Mode

Files saved immediately. See [Media Library Integration](media-library.md).

## Examples

### PDF Upload

```blade
<livewire:upload-handler.item
    :acceptsMimeTypes="['application/pdf']"
    :maxFileSize="20480"
/>
```

### Image with Compression

```blade
<livewire:upload-handler.item
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
    :sortable="true"
    :showImagePreview="true"
    :acceptsMimeTypes="['image/jpeg', 'image/png', 'image/webp']"
/>
```

Requires [Sortable.js](https://github.com/SortableJS/Sortable) loaded globally as `window.Sortable`.

### Load Existing File

```blade
<livewire:upload-handler.item
    savedFileDisk="users-avatars"
    :savedFilePath="$user->avatar_path"
/>
```

## Next Steps

- [Media Library](media-library.md) - Direct integration with Spatie Media Library
- [Events](events.md) - Listen to upload events
