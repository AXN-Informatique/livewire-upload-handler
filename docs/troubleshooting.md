# Troubleshooting

## Upload Fails

### File Too Large

**Symptom:** Upload stops or fails for large files.

**Solution:** Increase PHP limits in `php.ini`:

```ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
memory_limit = 256M
```

Restart web server after changes.

### Chunk Size Mismatch

**Symptom:** Chunked uploads fail or corrupt files.

**Solution:** Ensure `chunk_size` in config matches `upload_max_filesize`:

```php
'chunk_size' => bytes_to_int(ini_get('upload_max_filesize')),
```

### MIME Type Rejected

**Symptom:** "Invalid file type" error.

**Solution:** Check `acceptsMimeTypes` includes the file's MIME type:

```blade
<livewire:upload-handler.item
    :acceptsMimeTypes="['image/jpeg', 'image/png', 'image/webp']"
/>
```

## Images Not Showing

### Missing Glide Sign Key

**Symptom:** Images return 404 or error.

**Solution:** Generate and set `GLIDE_SIGN_KEY` in `.env`:

```bash
php -r "echo bin2hex(random_bytes(32));"
```

```env
GLIDE_SIGN_KEY=your-generated-key
```

### Wrong Image Driver

**Symptom:** Image processing fails.

**Solution:** Ensure GD or Imagick installed:

```bash
php -m | grep -i gd
php -m | grep -i imagick
```

Set correct driver in `.env`:

```env
GLIDE_IMAGE_DRIVER=gd
```

## Sortable Not Working

**Symptom:** Drag & drop doesn't work.

**Solution:** Load Sortable.js **before** `@livewireUploadHandlerScripts`:

```html
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>window.Sortable = Sortable;</script>
@livewireUploadHandlerScripts
```

## Compression Not Working

**Symptom:** Images not compressed.

**Solution:** Load Compressor.js **before** `@livewireUploadHandlerScripts`:

```html
<script src="https://cdn.jsdelivr.net/npm/compressorjs@latest/dist/compressor.min.js"></script>
<script>window.Compressor = Compressor;</script>
@livewireUploadHandlerScripts
```

## Assets Not Loading

### 404 on CSS/JS

**Symptom:** Console errors for missing assets.

**Solution:** Run build command:

```bash
npm install
npm run build
```

Ensure `dist/` contains compiled files.

### Cache Issues

Clear cache:

```bash
php artisan view:clear
php artisan cache:clear
```

## Media Library Issues

### Method Not Found

**Symptom:** `saveUploadedFile not handled by this component`

**Solution:** Use `MediaItem`/`MediaGroup` instead of `Item`/`Group`, or extend and implement the method.

### AutoSave Required

**Symptom:** Files not saving to Media Library.

**Solution:** Set `autoSave="true"`:

```blade
<livewire:upload-handler.media-item
    :model="$article"
    :autoSave="true"
/>
```

## Debug Mode

Enable Livewire debug to see events:

```php
// In component
public function boot()
{
    logger()->info('Component booted', [
        'component' => static::class,
        'properties' => $this->all(),
    ]);
}
```

Check browser console for JavaScript errors.

## Still Having Issues?

1. Check [GitHub Issues](https://github.com/AXN-Informatique/livewire-upload-handler/issues)
2. Review [configuration](configuration.md)
3. Verify [requirements](../README.md#requirements)
