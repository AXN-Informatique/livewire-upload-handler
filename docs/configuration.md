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

## Next Steps

- [Basic Usage](basic-usage.md) - Start using the components
