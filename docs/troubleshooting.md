---
title: Troubleshooting
order: 9
---

Troubleshooting
===============

Upload Fails
------------

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

### Upload Error With a Large Group (Livewire 4)

**Symptom:** With Livewire 4, actions on a group with many files fail with `TooManyComponentsException` or `PayloadTooLargeException`.

**Cause:** Each file of a group is a Livewire component bound to the group, and Livewire 4 limits the number of components and the size of a request (`livewire.payload.max_components` defaults to 200, `livewire.payload.max_size` to 1 MB).

**Solution:** Limit the number of files with `maxFilesNumber`, or raise these limits in `config/livewire.php`.

### Temporary Disk Configured to Throw (Livewire 4)

**Symptom:** With Livewire 4, uploaded file names cannot be read (`UnableToReadFile` on a `.json` file).

**Cause:** Livewire 4 looks for a `.json` meta file next to each temporary file. Files assembled from chunks by this package have no meta file, so the lookup fails when the temporary upload disk has `'throw' => true`.

**Solution:** Keep `'throw' => false` (Laravel default) on the disk used for temporary uploads (`livewire.temporary_file_upload.disk`).

Images Not Showing
------------------

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

Sortable Not Working
--------------------

**Symptom:** Drag & drop doesn't work.

**Solution:** Load Sortable.js **before** `@livewireUploadHandlerScripts`:

```html
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>window.Sortable = Sortable;</script>
@livewireUploadHandlerScripts
```

Compression Not Working
-----------------------

**Symptom:** Images not compressed.

**Solution:** Load Compressor.js **before** `@livewireUploadHandlerScripts`:

```html
<script src="https://cdn.jsdelivr.net/npm/compressorjs@latest/dist/compressor.min.js"></script>
<script>window.Compressor = Compressor;</script>
@livewireUploadHandlerScripts
```

Assets Not Loading
------------------

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

Media Library Issues
--------------------

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

Debug Mode
----------

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

Still Having Issues?
--------------------

1. Check [GitHub Issues](https://github.com/AXN-Informatique/livewire-upload-handler/issues)
2. Review [configuration](configuration.md)
3. Verify [requirements](./_index.md#requirements)
