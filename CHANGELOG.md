Changelog
=========

1.6.0 (unreleased)
------------------

- Add Livewire 4 support (Livewire 3 is still supported)
- Delete the `.json` meta files that Livewire 4 stores next to each uploaded chunk
- An error while finishing an upload (e.g. saving the file with `autoSave`) is now reported (`report()`) and displayed as an upload error, instead of failing the Livewire request
- Livewire 4: actions of components containing upload handler items (group, parent component bound with `wire:model`...) are held while an item request is in progress, as Livewire 3 does, so that an outdated item state is never sent
- Livewire 4: a failed group request (sorting, adding files) no longer blocks the following group/items actions
- Livewire 4: fix `CannotUpdateLockedPropertyException` on `compressorjsSettings` when uploading an image with Compressor.js settings (the settings are no longer mutated client-side)
- Livewire 4 (smart wire keys): fix the Livewire components placed after an upload handler component in a parent view being mounted again on the first parent update (a `media-group` bound with `wire:model` then failed on every request with `Typed property MediaItem::$model must not be accessed before initialization`)
- Fix group items not loaded from entities when the request has no session
- Fix the server-side `maxFileSize` check, which compared kilobytes instead of bytes (see UPGRADE.md)
- Add a test suite (Orchestra Testbench)


1.5.2 (2026-07-22)
------------------

- Add support for axn/laravel-glide 4


1.5.1 (2026-07-12)
------------------

- Slimmed Boost guidelines to pointer + critical pitfalls (details covered by the skill)


1.5.0 (2026-04-03)
------------------

- Add Laravel 13 support


1.4.1 (2026-03-11)
------------------

- Fix `resources/boost/` missing from composer archive


1.4.0 (2026-03-11)
------------------

- Add client-side image dimension validation (`minWidth`, `maxWidth`, `minHeight`, `maxHeight`)
- Add Laravel Boost assets (`resources/boost/`)


1.3.2 (2026-01-22)
------------------

- Fix theme loading


1.3.1 (2026-01-22)
------------------

- Add missing view `progress.blade.php` for theme `bootstrap-4`


1.3.0 (2026-01-22)
------------------

**Breaking changes if you used events**

- Rename events (to be catched by Alpine `x-on` directive):
  - `livewire-upload-handler:uploaded` to `luh-uploaded`
  - `livewire-upload-handler:canceled` to `luh-canceled`
  - `livewire-upload-handler:media-saved` to `luh-media-saved`
  - `livewire-upload-handler:media-deleted` to `luh-media-deleted`
- Add `inputBaseName` parameter to `luh-media-saved` and `luh-media-deleted` events
- Add `bootstrap-4` theme


1.2.2 (2026-01-18)
------------------

- Documentation: Add instructions for excluding temporary files from Git
  - Extended "Git Configuration" section in `docs/installation.md`
  - Document `livewire-tmp/` (Livewire) and `.livewire-upload-handler-glide-cache/` (this package)
  - Visible note in main `README.md`
  - Shell commands for automatic `.gitignore` creation


1.2.1 (2026-01-13)
------------------

- Add YAML front-matter to documentation for Savane compatibility
- Add `docs/index.md` as documentation entry point
- Add `UPGRADE.md` with migration guides


1.2.0 (2026-01-05)
------------------

**Breaking changes if you extend components**

- `Group`: Remove method `itemComponentParams`
- `Group`: Add method `commonTraits`
- `Group`: Add partial view `item-component`
- Property `savedFileDisk` can be `null` and is `null` by default
- Add option `showTemporaryFileWarning` (temporary file warning is no longer shown by default)
- Exception `MediaCollectionNotRegisteredException` when media collection is not registered
- Supports `wire:model` on Livewire Upload Handler components
- Fix `HandleMediaFromRequest`: single item must have "tmpName" or "id"


1.1.1 (2025-12-11)
------------------

**Need to republish view `group.blade.php` if you publish it**

- Fix common params overrides


1.1.0 (2025-12-11)
------------------

**Possible breaking changes if you extend components**

- Fix: Items not loaded when back from redirection with auto-save mode
- `Group`: Method `saveItemOrder` become `saveFileOrder` (file `id` directly pass instead of `itemId`)
- Supports loading existing file with `upload-handler.item` component
- Add option `--single` to command `make:upload-handler` and review stubs
- Refactoring for simplify component extension


1.0.1 (2025-12-03)
------------------

- Fix error with option `only-upload`


1.0.0 (2025-12-02)
------------------

- First release
