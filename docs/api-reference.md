# API Reference

## Components

### Item

Base component for single file upload.

**Properties:**

| Name | Type | Default | Description |
|------|------|---------|-------------|
| `itemId` | string | - | Unique identifier |
| `itemData` | array | `[]` | File metadata |
| `inputBaseName` | string | `'file'` | Form input name |
| `acceptsMimeTypes` | array | `[]` | Allowed MIME types |
| `maxFileSize` | int\|null | `null` | Max size in KB |
| `previewImage` | bool | `false` | Show image preview |
| `autoSave` | bool | `false` | Auto-save on upload |
| `onlyUpload` | bool | `false` | Hide file display |
| `sortable` | bool | `false` | Enable sorting |
| `compressorjsSettings` | array | `[]` | Compression options |
| `glidePreviewSettings` | array | config | Preview dimensions |
| `theme` | string\|null | config | CSS theme |
| `iconsTheme` | string\|null | config | Icons theme |

**Public Methods:**

- `deleteUploadingFile(): void` - Cancel upload in progress
- `deleteUploadedFile(): void` - Delete uploaded temp file
- `deleteSavedFile(): void` - Delete permanently saved file (override required)
- `downloadFile(): Response` - Download file
- `render(): View` - Render component

**Protected Methods (Override in subclasses):**

- `saveUploadedFile(TemporaryUploadedFile): void` - Save uploaded file
- `savedFileId(): ?string` - Get saved file ID
- `savedFileName(): ?string` - Get saved file name
- `savedImagePreviewUrl(): ?string` - Get saved image URL
- `savedFileExists(): bool` - Check if saved file exists
- `hasSavedFile(): bool` - Check if file is saved
- `viewName(): string` - View path

### Group

Base component for multiple file uploads.

**Properties:**

| Name | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array | `[]` | Array of items |
| `inputBaseName` | string | `'files'` | Form input base name |
| `sortable` | bool | `false` | Enable drag & drop sorting |
| *+ all Item properties* | | | |

**Public Methods:**

- `incrementItems(int): void` - Add multiple items
- `sortItems(array): void` - Update items order
- `render(): View` - Render component

**Protected Methods (Override in subclasses):**

- `addItem(array): string` - Add single item
- `saveItemOrder(string, int): void` - Save item order
- `itemComponentClassName(): string` - Item component class
- `itemComponentParams(string): array` - Item parameters
- `viewName(): string` - View path

### MediaItem

Extends `Item` for Spatie Media Library.

**Additional Properties:**

| Name | Type | Default | Description |
|------|------|---------|-------------|
| `model` | HasMedia | required | Model instance |
| `mediaCollection` | string | `'default'` | Collection name |
| `mediaProperties` | array | `[]` | Custom properties |
| `media` | Media\|null | `null` | Media model |

**Overridden Methods:**

- `saveUploadedFile(TemporaryUploadedFile): void` - Saves to Media Library
- `deleteSavedFile(): void` - Deletes media
- `downloadSavedFile(): Response` - Downloads media file

### MediaGroup

Extends `Group` for Spatie Media Library.

**Additional Properties:**

Same as `MediaItem`.

**Overridden Methods:**

- `saveItemOrder(string, int): void` - Updates `order_column`
- `itemComponentClassName(): string` - Returns `MediaItem::class`

## Enums

### MediaType

```php
enum MediaType: string
{
    case Image = 'image';
    case Video = 'video';
    case Audio = 'audio';
    case Document = 'document';
    case Archive = 'archive';
    case Other = 'other';
}
```

**Methods:**

- `static fromMimeType(string): self` - Detect type from MIME
- `isImage(): bool`
- `isVideo(): bool`
- `isAudio(): bool`
- `isDocument(): bool`
- `isArchive(): bool`
- `supportsPreview(): bool` - Returns `true` for Image/Video

### FileState

```php
enum FileState: string
{
    case Uploading = 'uploading';
    case Uploaded = 'uploaded';
    case Saved = 'saved';
    case Error = 'error';
    case Deleted = 'deleted';
}
```

**Methods:**

- `isUploading(): bool`
- `isComplete(): bool` - Returns `true` for Uploaded/Saved
- `hasError(): bool`

### AssetType

```php
enum AssetType: string
{
    case JavaScript = 'application/javascript';
    case CSS = 'text/css';
}
```

**Methods:**

- `static fromFilename(string): ?self`
- `mimeType(): string`
- `withCharset(): string` - Returns MIME with `; charset=utf-8`

## Exceptions

### UploadException

```php
class UploadException extends RuntimeException
```

**Factory Methods:**

- `static chunkProcessingFailed(Throwable): self`
- `static validationFailed(string): self`
- `static fileNotFound(string): self`
- `static invalidMimeType(string, array): self`
- `static fileTooLarge(int, int): self`

### FileNotHandledException

```php
class FileNotHandledException extends LogicException
```

**Factory Methods:**

- `static saveUploadedFile(string): self`
- `static deleteSavedFile(string): self`
- `static downloadSavedFile(string): self`
- `static saveItemOrder(string): self`

## Helper Functions

### bytes_to_int

```php
function bytes_to_int(string|int $value): int
```

Converts PHP ini values like `'8M'` to bytes.

**Examples:**

```php
bytes_to_int('8M')  // 8388608
bytes_to_int('1G')  // 1073741824
bytes_to_int('512K') // 524288
bytes_to_int(1024)  // 1024
```

### str_arr_to_dot

```php
function str_arr_to_dot(string $value): string
```

Converts array notation to dot notation.

**Example:**

```php
str_arr_to_dot('files[0][name]') // 'files.0.name'
```

## Routes

### Assets Route

```
GET /livewire-upload-handler/assets/{fileName}
```

Serves compiled CSS/JS from `dist/`.

### Glide Route

```
GET /livewire-upload-handler/glide/{disk}/{path}
```

Serves transformed images with query string parameters:
- `w` - Width
- `h` - Height
- `fit` - Fit mode (`crop`, `contain`, etc.)
- `s` - Signature (required)

Example: `/livewire-upload-handler/glide/public/images/photo.jpg?w=300&h=300&fit=crop&s=abc123`
