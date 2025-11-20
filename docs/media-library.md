# Media Library Integration

Direct integration with [Spatie Laravel Media Library](https://spatie.be/docs/laravel-medialibrary/).

## Setup

Your model must implement `HasMedia`:

```php
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png'])
            ->maxFileSize(10 * 1024 * 1024);
    }
}
```

## Single File (MediaItem)

```blade
<livewire:upload-handler.media-item
    :model="$article"
    mediaCollection="images"
    :autoSave="true"
/>
```

Files are saved directly to Media Library on upload.

## Multiple Files (MediaGroup)

```blade
<livewire:upload-handler.media-group
    :model="$article"
    mediaCollection="gallery"
    :sortable="true"
    :autoSave="true"
/>
```

With sorting, `order_column` is automatically updated.

## Custom Filters

```blade
<livewire:upload-handler.media-item
    :model="$product"
    mediaCollection="photos"
    :mediaFilters="['featured' => true]"
    :autoSave="true"
/>
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `model` | HasMedia | required | Model instance |
| `mediaCollection` | string | `'default'` | Collection name |
| `mediaFilters` | array | `[]` | Filters for retrieving media |
| `autoSave` | bool | `false` | Must be `true` for Media Library |

**Note**: `autoSave` must be `true` for MediaItem/MediaGroup. Manual mode not supported.

## MIME Types & Size

MIME types and max file size are inherited from Media Collection definition. You can override:

```blade
<livewire:upload-handler.media-item
    :model="$article"
    mediaCollection="images"
    :acceptsMimeTypes="['image/webp']"
    :maxFileSize="5120"
    :autoSave="true"
/>
```

## Events

```php
protected $listeners = [
    'livewire-upload-handler:media-saved' => 'onMediaSaved',
    'livewire-upload-handler:media-deleted' => 'onMediaDeleted',
];

public function onMediaSaved($mediaId)
{
    $media = Media::find($mediaId);
    // Process saved media
}
```

See [Events](events.md) for details.

## Example: Product Gallery

```blade
<div>
    <h2>Product Gallery</h2>
    <livewire:upload-handler.media-group
        :model="$product"
        mediaCollection="gallery"
        :sortable="true"
        :showImagePreview="true"
        :autoSave="true"
    />
</div>
```

```php
class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk('public');
    }
}
```

## Next Steps

- [Events](events.md) - Listen to media events
- [Advanced Usage](advanced-usage.md) - Custom handlers
