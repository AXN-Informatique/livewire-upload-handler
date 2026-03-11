---
name: upload-handler
description: Livewire Upload Handler usage patterns, component properties, events, and extension guide. Activate when implementing file uploads or extending upload components.
allowed-tools: Read, Glob
---

# Livewire Upload Handler

## Component Hierarchy

Two parallel hierarchies using a group/item architecture:

1. **Generic**: `Group` → `Item` (file storage you implement)
2. **Media Library**: `MediaGroup` → `MediaItem` (Spatie Media Library auto-save)

## Item Properties (Common)

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `autoSave` | bool | false | Save files immediately on upload |
| `onlyUpload` | bool | false | Show upload button only (no file display) |
| `showImagePreview` | bool | false | Enable Glide image thumbnails |
| `showFileSize` | bool | false | Display file size |
| `showTemporaryFileWarning` | bool | false | Warning for unsaved temp files |
| `acceptsMimeTypes` | array | [] | Allowed MIME types |
| `maxFileSize` | int | 0 | Max file size in KB (0 = unlimited) |
| `compressorjsSettings` | array | [] | Compressor.js options |

## Group Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `maxFilesNumber` | int | 0 | Max files (0 = unlimited) |
| `sortable` | bool | false | Enable drag-and-drop sorting |

## MediaItem/MediaGroup Properties

| Property | Type | Description |
|----------|------|-------------|
| `model` | HasMedia | Model instance to attach files to |
| `mediaCollection` | string | Media Library collection name |
| `mediaFilters` | array | Filters when retrieving media |

## Events

| Event | Payload | When |
|-------|---------|------|
| `luh-uploaded` | `inputBaseName`, `tmpName` | File uploaded (non-autoSave) |
| `luh-canceled` | `inputBaseName`, `tmpName` | Uploaded file deleted |
| `luh-media-saved` | `inputBaseName`, `mediaId` | File saved to Media Library |
| `luh-media-deleted` | `inputBaseName`, `mediaId` | Media file deleted |

## Usage Patterns

### Single File Upload (in a form)

```blade
<livewire:upload-handler.item
    inputBaseName="avatar"
    :acceptsMimeTypes="['image/jpeg', 'image/png']"
    :maxFileSize="2048"
    :showImagePreview="true"
/>
```

### Multiple Files with Media Library

```blade
<livewire:upload-handler.media-group
    :model="$project"
    mediaCollection="documents"
    :autoSave="true"
    :sortable="true"
    :maxFilesNumber="10"
/>
```

### Single Media Item

```blade
<livewire:upload-handler.media-item
    :model="$user"
    mediaCollection="avatar"
    :autoSave="true"
    :showImagePreview="true"
/>
```

## Extending Components

### Custom Item (non-Media Library)

Extend `Item` and implement storage methods:

```php
class DocumentItem extends \Axn\LivewireUploadHandler\Livewire\Item
{
    protected function saveUploadedFile(TemporaryUploadedFile $uploadedFile): void
    {
        // Save to your storage
    }

    public function deleteSavedFile(): void
    {
        // Delete from your storage
    }

    protected function savedFileName(): string
    {
        return $this->document->filename;
    }
}
```

### Custom Group

```php
class DocumentGroup extends \Axn\LivewireUploadHandler\Livewire\Group
{
    protected function itemComponentClassName(): string
    {
        return DocumentItem::class;
    }

    protected function initialEntities(): array|Collection
    {
        return $this->model->documents;
    }
}
```

### Scaffold with Artisan

```bash
# Group + Item + Common trait + views
php artisan make:upload-handler Project/Document

# Single item only
php artisan make:upload-handler Project/Avatar --single
```

## Theme System

- CSS classes: `resources/themes/css-classes/{theme}.php` (default: `bootstrap-5`)
- Icons: `resources/themes/icons/{theme}.php` (default: `fontawesome-7`)
- Publish: `php artisan vendor:publish --tag=livewire-upload-handler:themes`

## Configuration

Key config options in `config/livewire-upload-handler.php`:

- `theme` — CSS theme (default: `bootstrap-5`)
- `icons_theme` — Icons theme (default: `fontawesome-7`)
- `chunk_size` — Upload chunk size (auto from `upload_max_filesize`)
- `compressorjs_var` — Global JS variable for Compressor.js
- `sortablejs_var` — Global JS variable for Sortable.js
- `glide_image_driver` — `gd` or `imagick`
- `glide_sign_key` — Glide URL signing key

## Internal References

- docs/basic-usage.md
- docs/media-library.md
- docs/advanced-usage.md
- docs/customization.md
- docs/events.md
- docs/configuration.md
