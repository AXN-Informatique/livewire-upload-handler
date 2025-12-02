# Livewire Upload Handler

Modern file upload handler for Livewire 3 with chunked uploads, image previews via Glide, and Spatie Media Library integration.

## Features

- 📦 Chunked uploads for large files
- 🖼️ Image previews with Glide
- 🎯 Drag & drop support
- 🔄 Sortable files (with Sortable.js)
- ✅ MIME type & file size validation
- 🎨 Themeable (CSS classes + icons)
- 📱 Spatie Media Library integration
- 🌍 i18n (English + French)
- ⚡ Auto-save or manual mode

## Requirements

- PHP 8.4+
- Laravel 12+
- Livewire 3.1+

> Uses PHP 8.4 features: asymmetric visibility, enums, typed exceptions.

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

## Documentation

- **[Installation](docs/installation.md)** - Setup and configuration
- **[Configuration](docs/configuration.md)** - All config options
- **[Basic Usage](docs/basic-usage.md)** - Single & multiple uploads
- **[Media Library](docs/media-library.md)** - Spatie integration
- **[Customization](docs/customization.md)** - Themes, views, translations
- **[Advanced Usage](docs/advanced-usage.md)** - Custom handlers, enums, exceptions
- **[Events](docs/events.md)** - Livewire events reference
- **[Troubleshooting](docs/troubleshooting.md)** - Common issues
- **[API Reference](docs/api-reference.md)** - Complete component reference

## License

MIT License - see [LICENSE](LICENSE)

## Credits

- [AXN Informatique](https://github.com/AXN-Informatique)
- [Livewire](https://livewire.laravel.com/)
- [Glide](https://glide.thephpleague.com/)
- [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary/)
