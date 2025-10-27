# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

This is a Laravel package that provides file upload handling using Livewire 3, with support for Spatie Media Library integration. The package handles chunked file uploads, image previews via Glide, file validation, and drag-and-drop functionality.

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
- `Item.php`: Base item component for single file upload with chunking support
- `MediaGroup.php`: Extends Group to integrate with Spatie Media Library
- `MediaItem.php`: Extends Item to save files directly to Media Library

**Blade Component** (src/Components/):
- `Dropzone.php`: Simple Blade component wrapper for the dropzone view

### File Upload Flow

1. **Chunking**: Large files are split into chunks (configurable size from `upload_max_filesize`)
2. **Validation**: Files validated against MIME types and max size
3. **Storage**:
   - With `autoSave=true`: Files immediately saved to model/media library
   - With `autoSave=false`: Files kept as TemporaryUploadedFile until form submission
4. **Preview**: Image files generate thumbnails via Glide server

### Image Processing

The package integrates `axn/laravel-glide` for on-the-fly image manipulation:
- `GlideServerFactory.php`: Creates Glide servers per disk with signed URLs
- `GlideController.php`: Serves transformed images at `/livewire-upload-handler/glide/{disk}/{path}`
- Preview settings configurable per component or globally

### Theme System

The package uses a theme system (see `Livewire/Concerns/HasThemes.php`):
- CSS classes theme: Default is `bootstrap-5` (in `resources/themes/css-classes/`)
- Icons theme: Default is `fontawesome-7` (in `resources/themes/icons/`)
- Views can be published and customized per installation

### Routes

Registered in `routes/web.php`:
- `/livewire-upload-handler/assets/{fileName}`: Serves compiled JS/CSS from dist/
- `/livewire-upload-handler/glide/{disk}/{path}`: Image transformation endpoint

### Configuration

Main config in `config/livewire-upload-handler.php`:
- `chunk_size`: Based on PHP's `upload_max_filesize`
- `compressorjs_var`: Global JS variable for Compressor.js (optional)
- `sortablejs_var`: Global JS variable for Sortable.js (optional)
- `glide_*`: Glide server settings (driver, signing, preview dimensions)

### JavaScript Integration

The package requires two external libraries (not bundled):
- **Compressor.js**: Optional client-side image compression
- **Sortable.js**: Required if using sortable groups

Assets are loaded via Blade directives:
- `@livewireUploadHandlerScripts`: Injects config + compiled JS
- `@livewireUploadHandlerStyles`: Injects compiled CSS

### Component Properties

Key properties shared across Item/MediaItem:
- `autoSave`: If true, files saved immediately; if false, kept as temp files
- `onlyUpload`: If true, component shows only upload button (no file display)
- `previewImage`: Enable/disable image thumbnails
- `acceptsMimeTypes`: Array of allowed MIME types
- `maxFileSize`: Maximum file size in KB
- `compressorjsSettings`: Settings passed to Compressor.js
- `glidePreviewSettings`: Dimensions/fit settings for thumbnails

### Events Dispatched

- `livewire-upload-handler:uploaded`: When file upload completes (non-autoSave)
- `livewire-upload-handler:canceled`: When uploaded file is deleted
- `livewire-upload-handler:media-saved`: When file saved to Media Library
- `livewire-upload-handler:media-deleted`: When media file deleted

## Package Development Notes

### Extending Components

To create custom upload handlers:
1. Extend `Item` or `MediaItem`
2. Override methods like `saveUploadedFile()`, `savedFileId()`, `savedFileName()`, etc.
3. For groups, extend `Group` or `MediaGroup` and override `itemComponentClassName()`

### Testing Chunked Uploads

The chunked upload implementation follows the pattern from https://fly.io/laravel-bytes/chunked-file-upload-livewire/

Files are reassembled in `updatedChunkFile()` method by appending chunks to a temporary file until complete.

### Service Provider

`ServiceProvider.php` handles:
- Registering Livewire components with names like `upload-handler.group`
- Loading routes, views, translations
- Registering Blade directives for assets
- Publishing config, translations, views, and themes

### Webpack Build

The build system uses three separate webpack configs (in `webpack/` directory) that compile:
1. Non-minified JS (`scripts.js`)
2. Minified JS (`scripts.min.js`)
3. CSS bundle (`styles.css`)

After compilation, `build.js` merges partial manifests into final `dist/manifest.json`.
