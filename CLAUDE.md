# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

This is a modern Laravel 12 package (requiring PHP 8.4+) that provides file upload handling using Livewire 3, with support for Spatie Media Library integration. The package handles chunked file uploads, image previews via Glide, file validation, and drag-and-drop functionality.

**Modern PHP Features:** The codebase utilizes PHP 8.4 features including typed exceptions and enums for type safety.

## Development Commands

### Build Frontend Assets
```bash
npm run build
```
Compiles JavaScript and CSS using webpack. Creates both minified and non-minified JS builds, plus CSS bundle in `dist/` directory with a manifest.

### PHP Code Formatting
```bash
vendor/bin/pint
```
Formats PHP code according to Laravel Pint rules (configured in `pint.json`).

### PHP Refactoring (Rector)
```bash
vendor/bin/rector
```
Automated PHP refactoring (configured in `rector.php`).

### Install Dependencies
```bash
composer install
npm install
```

## Architecture

### Core Component Hierarchy

The package uses a group/item architecture with two parallel hierarchies:

1. **Generic File Uploads**: `Group` (container) → `Item` (individual file)
2. **Media Library Integration**: `MediaGroup` (extends Group) → `MediaItem` (extends Item)

Both hierarchies follow the same pattern where:
- Group components manage multiple file items with optional sorting (Sortable.js)
- Item components handle individual file upload/download/deletion with chunked uploads

### Key Livewire Components

**Base Components** (src/Livewire/):
- `Group.php`: Base group component for managing multiple file uploads
- `Item.php`: Base item component for single file upload with chunking support (`#[Isolate]`)
- `MediaGroup.php`: Extends Group to integrate with Spatie Media Library
- `MediaItem.php`: Extends Item to save files directly to Media Library (`#[Isolate]`)

**Blade Component** (src/Components/):
- `Dropzone.php`: Simple Blade component wrapper for the dropzone view (registered as `x-livewire-upload-handler-dropzone`)

### Traits / Concerns

Located in `src/Livewire/Concerns/`:

- **`Common`**: Shared properties for both Item and Group (`acceptsMimeTypes`, `maxFileSize`, `showFileSize`, `showImagePreview`, `showTemporaryFileWarning`, `autoSave`, `onlyUpload`, `compressorjsSettings`). Also provides `initialItemData()`, `initialItemParams()`, and `old()` methods.
- **`MediaCommon`**: Shared properties for MediaItem and MediaGroup (`model`, `mediaCollection`, `mediaFilters`). Validates media collection exists on boot, inherits MIME types and max file size from collection definition.
- **`HasThemes`**: Theme system (`theme`, `iconsTheme` properties). Loads CSS classes and icons from PHP config files with fallback chain: published vendor path → package resources path.

### File Upload Flow

1. **Chunking**: Large files are split into chunks (configurable size from `upload_max_filesize`)
2. **Validation**: Files validated against MIME types and max size
3. **Storage**:
   - With `autoSave=true`: Files immediately saved to model/media library
   - With `autoSave=false`: Files kept as TemporaryUploadedFile until form submission
4. **Preview**: Image files generate thumbnails via Glide server

### HandleMediaFromRequest Service

`HandleMediaFromRequest` (`src/HandleMediaFromRequest.php`) handles media saving from form submissions (non-autoSave mode):
- `single()`: Handle single file upload/replace/delete
- `multiple()`: Handle multiple files with ordering
- Supports `customizeMedia` closure for post-save customization

### Image Processing

The package integrates `axn/laravel-glide` for on-the-fly image manipulation:
- `GlideServerFactory.php`: Creates Glide servers per disk with signed URLs
- `GlideController.php`: Serves transformed images at the configured `glide_base_url`
- Preview settings configurable per component or globally

### Theme System

The package uses a theme system (see `Livewire/Concerns/HasThemes.php`):
- CSS classes theme: Default is `bootstrap-5` (in `resources/themes/css-classes/`)
- Icons theme: Default is `fontawesome-7` (in `resources/themes/icons/`)
- Available CSS themes: `bootstrap-4`, `bootstrap-5`
- Available icons themes: `fontawesome-7`
- Themed views (e.g., progress bars) in `resources/views/themes/{theme}/`
- Views can be published and customized per installation

### Routes

Registered in `routes/web.php`:
- `GET /livewire-upload-handler/assets/{fileName}`: Serves compiled JS/CSS from dist/ (`AssetsController`)
- `GET {glide_base_url}/{disk}/{path}`: Image transformation endpoint (`GlideController`), where `{disk}` is constrained to configured filesystem disk names

### Configuration

Main config in `config/livewire-upload-handler.php`:
- `theme`: CSS classes theme name (default: `bootstrap-5`)
- `icons_theme`: Icons theme name (default: `fontawesome-7`)
- `compressorjs_var`: Global JS variable for Compressor.js (default: `window.Compressor`)
- `sortablejs_var`: Global JS variable for Sortable.js (default: `window.Sortable`)
- `chunk_size`: Based on PHP's `upload_max_filesize`
- `glide_max_image_size`: Maximum pixels for Glide (default: `2000 * 2000`)
- `glide_image_driver`: `gd` or `imagick` (from `GLIDE_IMAGE_DRIVER` env)
- `glide_sign_key`: Secret key for signed Glide URLs (from `GLIDE_SIGN_KEY` env)
- `glide_base_url`: Base URL for Glide endpoint (default: `/livewire-upload-handler/glide`)

### JavaScript Integration

The package requires two external libraries (not bundled):
- **Compressor.js**: Optional client-side image compression
- **Sortable.js**: Required if using sortable groups

Assets are loaded via Blade directives:
- `@livewireUploadHandlerScripts`: Injects config (as `window.livewireUploadHandlerParams`) + compiled JS
- `@livewireUploadHandlerStyles`: Injects compiled CSS

### Component Properties

Key properties shared across Item/MediaItem (defined in `Common` trait):
- `acceptsMimeTypes`: Array of allowed MIME types
- `maxFileSize`: Maximum file size in KB (0 = unlimited)
- `showFileSize`: Enable/disable file size display
- `showImagePreview`: Enable/disable image thumbnails
- `showTemporaryFileWarning`: Show warning for unsaved temporary files
- `autoSave`: If true, files saved immediately; if false, kept as temp files
- `onlyUpload`: If true, component shows only upload button (no file display)
- `compressorjsSettings`: Settings passed to Compressor.js

Key properties from `HasThemes` trait:
- `theme`: CSS and views theme override (null = use config)
- `iconsTheme`: Icons theme override (null = use config)

Key properties for Group/MediaGroup:
- `inputBaseName`: Form input base name (default: `files` for Group, `file` for Item)
- `maxFilesNumber`: Maximum number of files allowed (0 = unlimited). For MediaGroup, automatically limited by Media Library collection `collectionSizeLimit`
- `sortable`: Enable drag & drop sorting

Key properties specific to MediaGroup/MediaItem (defined in `MediaCommon` trait):
- `model`: The HasMedia model instance to attach files to
- `mediaCollection`: Name of the Media Library collection (default: `'default'`)
- `mediaFilters`: Array of filters to apply when retrieving media from the collection

### Events Dispatched

- `luh-uploaded`: When file upload completes (non-autoSave). Payload: `inputBaseName`, `tmpName`
- `luh-canceled`: When uploaded file is deleted. Payload: `inputBaseName`, `tmpName`
- `luh-media-saved`: When file saved to Media Library. Payload: `inputBaseName`, `mediaId`
- `luh-media-deleted`: When media file deleted. Payload: `inputBaseName`, `mediaId`

### Internal Events

- `livewire-upload-handler:refresh`: Force components to refresh their data. Optional `inputBaseName` parameter to target specific component (null = refresh all)

## Package Development Notes

### Extending Components

To create custom upload handlers:
1. Use `php artisan make:upload-handler {name}` (or `--single` for single item)
2. Extend `Item` or `MediaItem`
3. Override methods like `saveUploadedFile()`, `deleteSavedFile()`, `initialEntity()`, etc.
4. For groups, extend `Group` or `MediaGroup` and override `itemComponentClassName()` and `initialEntities()`

Key methods available in Item component:
- `fileMimeType()`: Returns the MIME type of the current file (uploaded or saved)
- `fileType()`: Returns a `FileType` enum representing the file category
- `fileSize()`: Returns the file size in bytes
- `fileName()`: Returns the file name
- `fileDisk()`: Returns the storage disk name
- `filePath()`: Returns the file path relative to disk
- `fileExists()`: Whether the file exists on disk
- `glideUrl(array $params)`: Generate a Glide URL for image transformation
- `hasUploadedFile()`: Whether a temporary uploaded file exists
- `hasSavedFile()`: Whether a permanently saved file exists

### Artisan Command

`make:upload-handler {name} [--single] [--force]` generates:
- **Default (group + item)**: `Item.php`, `Group.php`, `Concerns/{Name}Common.php` trait, and corresponding views
- **Single mode** (`--single`): Single component class and view
- Uses stubs from `resources/stubs/`

### Helper Functions

Located in `src/helpers.php`:
- `bytes_to_int(string|int)`: Converts PHP size strings (e.g., `'2M'`, `'512K'`) to bytes
- `str_arr_to_dot(string)`: Converts array notation (`files[0][name]`) to dot notation (`files.0.name`)

### Service Provider

`ServiceProvider.php` handles:
- Registering Livewire components:
  - `upload-handler.group` → `Group`
  - `upload-handler.item` → `Item`
  - `upload-handler.media-group` → `MediaGroup`
  - `upload-handler.media-item` → `MediaItem`
- Registering Blade component: `x-livewire-upload-handler-dropzone` → `Dropzone`
- Loading routes, views, translations
- Registering Blade directives for assets (`@livewireUploadHandlerScripts`, `@livewireUploadHandlerStyles`)
- Registering `MakeUploadHandlerCommand`
- Publishing config, translations, views, and themes

### Webpack Build

The build system uses three separate webpack configs (in `webpack/` directory) that compile:
1. Non-minified JS (`scripts.js`)
2. Minified JS (`scripts.min.js`)
3. CSS bundle (`styles.css`)

After compilation, `build.js` merges partial manifests into final `dist/manifest.json`.

### Translations

Located in `lang/`:
- `en/` and `fr/` directories
- Files: `actions.php`, `errors.php`, `messages.php`
- Namespace: `livewire-upload-handler`

## Modern PHP 8.4 Features

### Enums

The package uses enums for type-safe constants:

- **`FileType`** (`src/Enums/FileType.php`): Represents file types with MIME type detection (Image, Video, Audio, Document, Archive, Other). Includes helper methods like `isImage()`, `isVideo()`, `supportsPreview()`, etc.
- **`AssetType`** (`src/Enums/AssetType.php`): Represents asset types for the assets controller (JavaScript, CSS)

### Typed Exceptions

Custom exceptions for better error handling:

- **`MethodNotImplementedException`** (`src/Exceptions/MethodNotImplementedException.php`): For unimplemented methods in base classes, with clear error messages guiding users to either implement the method or use Media Library components. Factory methods: `saveUploadedFile()`, `deleteSavedFile()`, `saveFileOrder()`
- **`MediaCollectionNotRegisteredException`** (`src/Exceptions/MediaCollectionNotRegisteredException.php`): Thrown when a media collection is not registered on the model
- **`MediaCannotBeRetrievedException`** (`src/Exceptions/MediaCannotBeRetrievedException.php`): Thrown when media doesn't belong to model or collection. Factory methods: `doesNotBelongToModel()`, `doesNotBelongToCollection()`

### Type Safety

All classes use:
- `declare(strict_types=1)` at the top
- Strict type hints for parameters and return types
- PHPDoc for array shapes (e.g., `@param array{id?: int|string|null, order?: int, deleted?: bool} $data`)
- `instanceof` checks instead of null comparisons for consistency

## Laravel Boost Assets

The package provides Laravel Boost integration assets in `resources/boost/`:
- **Guidelines** (`guidelines/core.blade.php`): Package overview for AI assistants
- **Skill** (`skills/upload-handler/SKILL.md`): Detailed usage patterns, component properties, events, and extension guide

**Important:** These files must be kept up to date when components, configuration keys, or usage patterns change. When adding, renaming, or removing components or config options, update the corresponding Boost assets accordingly.
