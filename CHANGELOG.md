Changelog
=========

1.X.X (2026-01-XX)
------------------

- Documentation : Ajout d'instructions pour exclure les fichiers temporaires de Git
  - Section "Git Configuration" étendue dans `docs/installation.md`
  - Documentation de `livewire-tmp/` (Livewire) et `.livewire-upload-handler-glide-cache/` (ce package)
  - Note visible dans le `README.md` principal
  - Commandes shell pour création automatique des `.gitignore`


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
