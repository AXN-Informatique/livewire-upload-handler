---
title: Events
order: 8
---

# Events

All events are dispatched via Livewire's `dispatch()` method.

## Item Events

### `livewire-upload-handler:uploaded`

Fired when file upload completes (manual mode only, not autoSave).

**Parameters:**
- `inputBaseName` (string): Component input name
- `tmpName` (string): Livewire temporary filename

```php
#[On('livewire-upload-handler:uploaded')]
public function onUploaded(string $inputBaseName, string $tmpName)
{
    $file = TemporaryUploadedFile::createFromLivewire($tmpName);
    // Process file
}
```

### `livewire-upload-handler:canceled`

Fired when uploaded file is deleted before save.

**Parameters:**
- `inputBaseName` (string)
- `tmpName` (string)

```php
#[On('livewire-upload-handler:canceled')]
public function onCanceled(string $inputBaseName, string $tmpName)
{
    logger()->info('Upload canceled', ['file' => $tmpName]);
}
```

## Media Library Events

### `livewire-upload-handler:media-saved`

Fired when file saved to Media Library (autoSave mode).

**Parameters:**
- `mediaId` (int): Saved media model ID

```php
#[On('livewire-upload-handler:media-saved')]
public function onMediaSaved(int $mediaId)
{
    $media = Media::find($mediaId);
    // Process media (e.g., generate conversions, notify user)
}
```

### `livewire-upload-handler:media-deleted`

Fired when media file deleted.

**Parameters:**
- `mediaId` (int)

```php
#[On('livewire-upload-handler:media-deleted')]
public function onMediaDeleted(int $mediaId)
{
    logger()->info('Media deleted', ['id' => $mediaId]);
}
```

## Example: Real-time Notifications

```php
class ArticleForm extends Component
{
    public Article $article;

    #[On('livewire-upload-handler:media-saved')]
    public function notifyImageSaved(int $mediaId)
    {
        $this->dispatch('notify',
            message: 'Image uploaded successfully!',
            type: 'success'
        );
    }
}
```

## Example: Processing on Upload

```php
#[On('livewire-upload-handler:media-saved')]
public function generateThumbnails(int $mediaId)
{
    $media = Media::find($mediaId);

    // Queue thumbnail generation
    GenerateThumbnails::dispatch($media);
}
```

## Next Steps

- [Advanced Usage](./advanced-usage.md) - Extend components
- [Troubleshooting](./troubleshooting.md) - Common issues
