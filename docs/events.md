# Events

All events are dispatched via Livewire's `dispatch()` method.

## Item Events

### `livewire-upload-handler:uploaded`

Fired when file upload completes (manual mode only, not autoSave).

**Parameters:**
- `inputBaseName` (string): Component input name
- `tmpName` (string): Livewire temporary filename

```php
protected $listeners = [
    'livewire-upload-handler:uploaded' => 'onUploaded',
];

public function onUploaded($inputBaseName, $tmpName)
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
public function onCanceled($inputBaseName, $tmpName)
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
protected $listeners = [
    'livewire-upload-handler:media-saved' => 'onMediaSaved',
];

public function onMediaSaved($mediaId)
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
public function onMediaDeleted($mediaId)
{
    logger()->info('Media deleted', ['id' => $mediaId]);
}
```

## Example: Real-time Notifications

```php
class ArticleForm extends Component
{
    public Article $article;

    protected $listeners = [
        'livewire-upload-handler:media-saved' => 'notifyImageSaved',
    ];

    public function notifyImageSaved($mediaId)
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
protected $listeners = [
    'livewire-upload-handler:media-saved' => 'generateThumbnails',
];

public function generateThumbnails($mediaId)
{
    $media = Media::find($mediaId);

    // Queue thumbnail generation
    GenerateThumbnails::dispatch($media);
}
```

## Next Steps

- [API Reference](api-reference.md) - Complete reference
