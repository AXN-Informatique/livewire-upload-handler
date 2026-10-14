Upgrade Guide
=============

## From 1.5.x to 1.6.x

No breaking change: the package now supports both Livewire 3 and Livewire 4.

### Behavior change if you extend components

- **`Item`**: an exception thrown by `uploadFinished()` (and so by `saveUploadedFile()` with `autoSave`) is now reported (`report()`), the uploaded file is deleted and the upload is flagged as failed (`hasErrorOnUpload`). Validation exceptions are still thrown.

### If you upgrade your application to Livewire 4

- Keep `@livewireUploadHandlerScripts` after `@livewireScripts`, and self-close the `<livewire:upload-handler.* />` tags
- Large groups may reach the Livewire 4 request limits, see [Troubleshooting](docs/troubleshooting.md)
- Keep `'throw' => false` on the temporary upload disk, see [Troubleshooting](docs/troubleshooting.md)


## From 1.1.x to 1.2.x

### Breaking changes if you extend components

- **`Group`**: Method `itemComponentParams` has been removed
- **`Group`**: New method `commonTraits` added
- **`Group`**: New partial view `item-component` added
- Property `savedFileDisk` can be `null` and is `null` by default

### New features

- Support for `wire:model` on Livewire Upload Handler components
- New option `showTemporaryFileWarning` (temporary file warning is no longer shown by default)

### Exceptions

- New exception `MediaCollectionNotRegisteredException` when media collection is not registered

### Fixes

- `HandleMediaFromRequest`: single item must have "tmpName" or "id"

### If you published views

Republish the `group.blade.php` view:

```bash
php artisan vendor:publish --tag=livewire-upload-handler:views --force
```


## From 1.0.x to 1.1.x

### Breaking changes if you extend components

- **`Group`**: Method `saveItemOrder` becomes `saveFileOrder` (file `id` is passed directly instead of `itemId`)

### If you published views

Republish the `group.blade.php` view:

```bash
php artisan vendor:publish --tag=livewire-upload-handler:views --force
```
