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
| `showImagePreview` | bool | `false` | Show image preview |
| `autoSave` | bool | `false` | Auto-save on upload |
| `onlyUpload` | bool | `false` | Hide file display |
| `sortable` | bool | `false` | Enable sorting |
| `compressorjsSettings` | array | `[]` | Compression options |
| `theme` | string\|null | config | CSS theme |
| `iconsTheme` | string\|null | config | Icons theme |

**Public Methods:**

- `deleteUploadingFile(): void` - Cancel upload in progress
- `deleteUploadedFile(): void` - Delete uploaded temp file
- `deleteSavedFile(): void` - Delete permanently saved file (override required)
- `downloadFile(): Response` - Download file
- `render(): View` - Render component

**Protected Methods (Override in subclasses):**

- `initialItemData(array $old): array` - To provide additionnal data to item, like file description
- `saveUploadedFile(TemporaryUploadedFile): void` - Save uploaded file
- `savedFileDisk(): string` - Get saved file disk
- `savedFilePath(): string` - Get saved file path
- `savedFileName(): string` - Get saved file name
- `savedFileSize(): int` - Get saved file size (in KB)
- `savedFileMimeType(): string` - Get saved file MIME type

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
- `saveFileOrder(string|int, int): void` - Save file order
- `itemComponentClassName(): string` - Item component class
- `itemComponentParams(string): array` - Item parameters

### MediaItem

Extends `Item` for Spatie Media Library.

**Additional Properties:**

| Name | Type | Default | Description |
|------|------|---------|-------------|
| `model` | HasMedia | required | Model instance |
| `mediaCollection` | string | `'default'` | Collection name |
| `mediaFilters` | array | `[]` | Filters for retrieving media |
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

- `saveFileOrder(string|int, int): void` - Updates `order_column`
- `itemComponentClassName(): string` - Returns `MediaItem::class`

## Enums

### FileType

```php
enum FileType: string
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

### MethodNotImplementedException

```php
class MethodNotImplementedException extends LogicException
```

**Factory Methods:**

- `static saveUploadedFile(string): self`
- `static deleteSavedFile(string): self`
- `static downloadSavedFile(string): self`
- `static saveFileOrder(string): self`
- `static savedFileDisk(string): self`
- `static savedFilePath(string): self`
- `static savedFileName(string): self`
- `static savedFileSize(string): self`
- `static savedFileMimeType(string): self`

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
