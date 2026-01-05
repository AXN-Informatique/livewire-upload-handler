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
            ->useDisk('articles-images');
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
    :model="$article"
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

## Auto-Save vs Manual

### Manual Mode (default)

Files stored temporarily until form submission:

```blade
<form action="{!! route('article.files.store', $article) !!}" method="POST">
    @csrf

    {{-- ===== Single ===== --}}
    <livewire:upload-handler.media-item
        inputBaseName="article_file"
        :model="$article"
        mediaCollection="file"
    />

    {{-- ===== Multiple ===== --}}
    <livewire:upload-handler.media-group
        inputBaseName="article_files"
        :model="$article"
        mediaCollection="files"
    />

    <button type="submit">Save</button>
</form>
```

Service `Axn\LivewireUploadHandler\HandleMediaFromRequest` can be used to handle save:

```php
use Axn\LivewireUploadHandler\HandleMediaFromRequest;

class ArticleController
{
    public function store(Article $article, Request $request, HandleMediaFromRequest $handleMediaFromRequest)
    {
        // ===== Single =====
        $handleMediaFromRequest->single(
            data: $request->post('article_file'),
            model: $article,
            mediaCollection: 'file'
        );

        // ===== Multiple =====
        $handleMediaFromRequest->multiple(
            data: $request->post('article_files'),
            model: $article,
            mediaCollection: 'files',
            // Optionnal... if you want to customize the media:
            customizeMedia: function (Media $media, array $data) {
                $media->name = $data['name']; // from input with name "article_files[$itemId][name]"
                // You do not need to execute `$media->save()`: it is already done into the service
            }
        );
    }
}
```

### Manual Mode inside another Livewire Component

If a Livewire Upload Handler component is used inside another Livewire component, and you want to handle save into this component
(for example, on a `wire:save` action), you can use `wire:model` to get data from Livewire Upload Handler component:

```blade
<livewire:article-form :$article />
```

```blade
{{-- resources/views/livewire/article-form.blade.php --}}

<div wire:submit="store">
    {{-- ===== Single ===== --}}
    <livewire:upload-handler.media-item
        inputBaseName="article_file"
        :model="$article"
        mediaCollection="file"
        wire:model="articleFile"
    />

    {{-- ===== Multiple ===== --}}
    <livewire:upload-handler.media-group
        inputBaseName="article_files"
        :model="$article"
        mediaCollection="files"
        wire:model="articleFiles"
    />

    <button type="submit">Save</button>
</div>
```

```php
// app/Livewire/ArticleForm.php

use Axn\LivewireUploadHandler\HandleMediaFromRequest;

class ArticleForm extends Component
{
    public Article $article;

    // Must be null to prevent Livewire erasing the data initialized
    // inside Livewire Upload Handler components
    public ?array $articleFile = null;
    public ?array $articleFiles = null;

    public function store()
    {
        // ===== Single =====
        app(HandleMediaFromRequest::class)->single(
            data: $this->articleFile,
            model: $article,
            mediaCollection: 'file'
        );

        // ===== Multiple =====
        app(HandleMediaFromRequest::class)->multiple(
            data: $this->articleFiles,
            model: $article,
            mediaCollection: 'files',
        );

        // Force Livewire Upload Handler components to refresh data
        $this->dispatch('livewire-upload-handler:refresh', inputBaseName: 'article_file');
        $this->dispatch('livewire-upload-handler:refresh', inputBaseName: 'article_files');

        // You can also send event without `inputBaseName` param to refresh
        // all Livewire Upload Handler components
        $this->dispatch('livewire-upload-handler:refresh');
    }
}
```


### Auto-Save Mode

Handled internally by the component `MediaItem`. Nothing more is needed.

If you want to customize the save process, you need to extend component.
See [Advanced Usage](advanced-usage.md) for details.

## Events

```php
#[On('livewire-upload-handler:media-saved')]
public function onMediaSaved(int $mediaId)
{
    // Some action on media saved
}

#[On('livewire-upload-handler:media-deleted')]
public function onMediaDeleted(int $mediaId)
{
    // Some action on media deleted
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
            ->useDisk('products-gallery');
    }
}
```

## Next Steps

- [Events](events.md) - Listen to media events
- [Advanced Usage](advanced-usage.md) - Custom components
