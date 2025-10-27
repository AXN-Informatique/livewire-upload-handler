# Configuration

File: `config/livewire-upload-handler.php`

## Theme Settings

```php
'theme' => 'bootstrap-5',
'icons_theme' => 'fontawesome-7',
```

- `theme`: CSS classes theme name
- `icons_theme`: Icons theme name

## JavaScript Libraries

```php
'compressorjs_var' => 'window.Compressor',
'sortablejs_var' => 'window.Sortable',
```

Global variables for optional external libraries (must be loaded separately).

## Upload Settings

```php
'chunk_size' => bytes_to_int(ini_get('upload_max_filesize')),
```

Size of each chunk for large file uploads. Defaults to PHP's `upload_max_filesize`.

## Glide (Image Processing)

```php
'glide_max_image_size' => 2000 * 2000,
'glide_image_driver' => env('GLIDE_IMAGE_DRIVER', 'gd'),
'glide_sign_key' => env('GLIDE_SIGN_KEY'),
'glide_base_url' => '/livewire-upload-handler/glide',
```

- `glide_max_image_size`: Maximum pixels (width × height)
- `glide_image_driver`: `'gd'` or `'imagick'`
- `glide_sign_key`: Secret key for signed URLs (required for security)
- `glide_base_url`: Base URL for image transformation endpoint

### Preview Settings

```php
'glide_preview_settings' => [
    'w' => 70,
    'h' => 70,
    'fit' => 'crop',
],
```

Default thumbnail dimensions. Can be overridden per component.

## Environment Variables

In `.env`:

```env
GLIDE_IMAGE_DRIVER=gd
GLIDE_SIGN_KEY=your-64-char-hex-string
```

Generate sign key:

```bash
php -r "echo bin2hex(random_bytes(32));"
```

## PHP Settings

For large file uploads, adjust in `php.ini`:

```ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
memory_limit = 256M
```

## Next Steps

- [Basic Usage](basic-usage.md) - Start using the components
