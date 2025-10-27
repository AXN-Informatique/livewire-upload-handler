# Livewire Upload Handler

A Laravel package providing powerful file upload handling using Livewire 3, with seamless Spatie Media Library integration.

## Features

- 📦 **Chunked uploads** - Handle large files by splitting them into smaller chunks
- 🖼️ **Image previews** - Automatic thumbnail generation using Glide
- 🎯 **Drag & drop** - User-friendly dropzone interface
- 🔄 **Sortable files** - Reorder uploaded files with drag & drop (Sortable.js)
- ✅ **File validation** - MIME type and file size validation
- 🎨 **Themeable** - Customizable CSS classes and icons
- 📱 **Media Library integration** - Direct integration with Spatie Media Library
- 🌍 **Multilingual** - English and French translations included
- ⚡ **Auto-save or manual** - Choose between immediate or deferred file storage

## Requirements

- PHP 8.4+
- Laravel 12.0+
- Livewire 3.1+

> **Note:** This package uses modern PHP 8.4 features including asymmetric visibility, property hooks, enums, and typed exceptions for better type safety and developer experience.

## Installation

Install the package via Composer:

```bash
composer require axn/livewire-upload-handler
```

### Publish Assets (Optional)

Publish configuration:
```bash
php artisan vendor:publish --tag=livewire-upload-handler:config
```

Publish translations:
```bash
php artisan vendor:publish --tag=livewire-upload-handler:translations
```

Publish views:
```bash
php artisan vendor:publish --tag=livewire-upload-handler:views
```

Publish themes:
```bash
php artisan vendor:publish --tag=livewire-upload-handler:themes
```

## Basic Usage

### 1. Include Assets in Your Layout

Add these Blade directives to your layout file:

```blade
<head>
    @livewireStyles
    @livewireUploadHandlerStyles
</head>
<body>
    <!-- Your content -->

    @livewireScripts
    @livewireUploadHandlerScripts
</body>
```

### 2. Single File Upload

For a simple single file upload:

```blade
<livewire:upload-handler.item
    wire:model="file"
    :acceptsMimeTypes="['image/jpeg', 'image/png']"
    :maxFileSize="10240"
/>
```

### 3. Multiple Files Upload (Group)

For multiple file uploads with sorting:

```blade
<livewire:upload-handler.group
    wire:model="files"
    :sortable="true"
    :acceptsMimeTypes="['image/jpeg', 'image/png', 'application/pdf']"
/>
```

### 4. Media Library Integration

For direct integration with Spatie Media Library:

```blade
<livewire:upload-handler.media-item
    :model="$article"
    mediaCollection="images"
    :autoSave="true"
/>
```

Or for multiple files:

```blade
<livewire:upload-handler.media-group
    :model="$article"
    mediaCollection="gallery"
    :sortable="true"
    :autoSave="true"
/>
```

## Configuration

The package comes with sensible defaults. Key configuration options in `config/livewire-upload-handler.php`:

```php
return [
    // UI Theme (CSS classes)
    'theme' => 'bootstrap-5',

    // Icons theme
    'icons_theme' => 'fontawesome-7',

    // Chunk size for large file uploads (defaults to upload_max_filesize)
    'chunk_size' => bytes_to_int(ini_get('upload_max_filesize')),

    // Glide image manipulation settings
    'glide_image_driver' => env('GLIDE_IMAGE_DRIVER', 'gd'),
    'glide_sign_key' => env('GLIDE_SIGN_KEY'),

    // Preview thumbnail dimensions
    'glide_preview_settings' => [
        'w' => 70,
        'h' => 70,
        'fit' => 'crop',
    ],
];
```

## Component Properties

### Common Properties

Both `Item` and `Group` components accept these properties:

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `acceptsMimeTypes` | array | `[]` | Allowed MIME types (e.g., `['image/jpeg', 'image/png']`) |
| `maxFileSize` | int\|null | `null` | Maximum file size in KB |
| `previewImage` | bool | `false` | Enable image thumbnail previews |
| `autoSave` | bool | `false` | Automatically save files on upload |
| `onlyUpload` | bool | `false` | Show only upload button (hide file display) |
| `compressorjsSettings` | array | `[]` | Settings for Compressor.js (requires library) |
| `glidePreviewSettings` | array | config | Custom preview dimensions |

### Group-Specific Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `sortable` | bool | `false` | Enable drag & drop sorting (requires Sortable.js) |
| `inputBaseName` | string | `'files'` | Base name for form inputs |

### Media Library Properties

When using `MediaItem` or `MediaGroup`:

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `model` | HasMedia | required | The model implementing `HasMedia` |
| `mediaCollection` | string | `'default'` | Media collection name |
| `mediaProperties` | array\|null | `null` | Custom properties for media items |

## External Dependencies

The package requires these JavaScript libraries to be included in your project:

### Optional: Compressor.js

For client-side image compression before upload:

```html
<script src="https://cdn.jsdelivr.net/npm/compressorjs@latest/dist/compressor.min.js"></script>
<script>
    window.Compressor = Compressor;
</script>
```

Then configure compression in your component:

```blade
<livewire:upload-handler.item
    :compressorjsSettings="[
        'quality' => 0.8,
        'maxWidth' => 1920,
        'maxHeight' => 1080,
    ]"
/>
```

### Required for Sorting: Sortable.js

For drag & drop file reordering:

```html
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    window.Sortable = Sortable;
</script>
```

## Events

The components dispatch Livewire events you can listen to:

### Item Events

- **`livewire-upload-handler:uploaded`** - Fired when file upload completes (non-autoSave mode)
  ```php
  ['inputBaseName' => string, 'tmpName' => string]
  ```

- **`livewire-upload-handler:canceled`** - Fired when uploaded file is deleted
  ```php
  ['inputBaseName' => string, 'tmpName' => string]
  ```

### Media Events

- **`livewire-upload-handler:media-saved`** - Fired when file is saved to Media Library
  ```php
  ['mediaId' => int]
  ```

- **`livewire-upload-handler:media-deleted`** - Fired when media file is deleted
  ```php
  ['mediaId' => int]
  ```

## Theming

The package supports custom themes for both CSS classes and icons.

### CSS Themes

Default theme is `bootstrap-5`. Create custom themes in `resources/vendor/livewire-upload-handler/themes/css-classes/`:

```php
// resources/vendor/livewire-upload-handler/themes/css-classes/my-theme.php
return [
    'dropzone' => 'my-dropzone-class',
    'item' => 'my-item-class',
    // ... other classes
];
```

Then set in config:
```php
'theme' => 'my-theme',
```

### Icon Themes

Default is `fontawesome-7`. Create custom icon themes in `resources/vendor/livewire-upload-handler/themes/icons/`:

```php
// resources/vendor/livewire-upload-handler/themes/icons/my-icons.php
return [
    'upload' => '<svg>...</svg>',
    'download' => '<svg>...</svg>',
    'delete' => '<svg>...</svg>',
    'sort' => '<svg>...</svg>',
];
```

## Advanced Usage

### Exception Handling

The package uses typed exceptions for better error handling:

```php
use Axn\LivewireUploadHandler\Exceptions\UploadException;
use Axn\LivewireUploadHandler\Exceptions\FileNotHandledException;

// Catch upload exceptions
try {
    // Upload logic
} catch (UploadException $e) {
    // Handle upload-specific errors
    logger()->error('Upload failed', ['exception' => $e]);
}

// FileNotHandledException is thrown when required methods aren't implemented
catch (FileNotHandledException $e) {
    // This guides you to either implement the method or use MediaItem/MediaGroup
}
```

### Using Enums

The package provides type-safe enums:

```php
use Axn\LivewireUploadHandler\Enums\MediaType;

$mimeType = 'image/jpeg';
$mediaType = MediaType::fromMimeType($mimeType);

if ($mediaType->isImage()) {
    // Handle image
}

if ($mediaType->supportsPreview()) {
    // Generate preview
}
```

### Custom Upload Handler

Create a custom item component by extending the base:

```php
namespace App\Livewire;

use Axn\LivewireUploadHandler\Livewire\Item;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CustomUploadItem extends Item
{
    protected function saveUploadedFile(TemporaryUploadedFile $uploadedFile): void
    {
        // Custom save logic
        $path = $uploadedFile->storeAs('uploads', $uploadedFile->getClientOriginalName());

        // Update your model
        $this->dispatch('file-saved', path: $path);
    }

    protected function savedFileName(): ?string
    {
        return $this->itemData['filename'] ?? null;
    }
}
```

### Custom Group Handler

```php
namespace App\Livewire;

use Axn\LivewireUploadHandler\Livewire\Group;
use App\Livewire\CustomUploadItem;

class CustomUploadGroup extends Group
{
    protected function itemComponentClassName(): string
    {
        return CustomUploadItem::class;
    }

    protected function saveItemOrder(string $itemId, int $order): void
    {
        // Custom ordering logic
    }
}
```

## How It Works

### Chunked Upload Process

1. Large files are split into chunks on the client side
2. Each chunk is uploaded sequentially via Livewire
3. Server reassembles chunks into the complete file
4. File is validated (MIME type, size)
5. Depending on `autoSave`:
   - `true`: File immediately saved to storage/Media Library
   - `false`: File kept as `TemporaryUploadedFile` until form submission

### Auto-Save vs Manual Save

**Auto-Save Mode** (`autoSave="true"`):
- Files are immediately saved when upload completes
- Best for direct file uploads without forms
- Works great with Media Library integration

**Manual Save Mode** (`autoSave="false"`):
- Files stored temporarily until form submission
- Use `wire:model` to bind to your component property
- Process files in your form submission handler

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Credits

- [AXN Informatique](https://github.com/AXN-Informatique)
- Built with [Livewire](https://livewire.laravel.com/)
- Image manipulation by [Glide](https://glide.thephpleague.com/)
- Media Library by [Spatie](https://spatie.be/docs/laravel-medialibrary/)
