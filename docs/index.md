---
title: Livewire Upload Handler
order: 1
---

# Livewire Upload Handler

Modern file upload handler for Livewire 3 with chunked uploads, image previews via Glide, and Spatie Media Library integration.

## Features

- Chunked uploads for large files
- Image previews with Glide
- Drag & drop support
- Sortable files (with Sortable.js)
- MIME type & file size validation
- Themeable (CSS classes + icons)
- Spatie Media Library integration
- i18n (English + French)
- Auto-save or manual mode

## Requirements

- PHP 8.4+
- Laravel 12+
- Livewire 3.1+

## Quick Start

```bash
composer require axn/livewire-upload-handler
```

Add to your layout:

```blade
<head>
    @livewireStyles
    @livewireUploadHandlerStyles
</head>
<body>
    @livewireScripts
    @livewireUploadHandlerScripts
</body>
```

Single file upload:

```blade
<livewire:upload-handler.item />
```

## Table of Contents

- [Installation](./installation.md)
- [Configuration](./configuration.md)
- [Basic Usage](./basic-usage.md)
- [Media Library](./media-library.md)
- [Customization](./customization.md)
- [Advanced Usage](./advanced-usage.md)
- [Events](./events.md)
- [Troubleshooting](./troubleshooting.md)
