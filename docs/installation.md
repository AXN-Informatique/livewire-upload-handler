---
title: Installation
order: 2
---

# Installation

## Install Package

```bash
composer require axn/livewire-upload-handler
```

## Add Blade Directives

In your layout file (e.g., `resources/views/layouts/app.blade.php`):

```blade
<head>
    @livewireStyles
    @livewireUploadHandlerStyles
</head>
<body>
    <!-- Your content -->

    @livewireScripts
    @livewireUploadHandlerScripts
</body>
```

## Publish Assets (Optional)

### Config

```bash
php artisan vendor:publish --tag=livewire-upload-handler:config
```

Creates `config/livewire-upload-handler.php`

### Translations

```bash
php artisan vendor:publish --tag=livewire-upload-handler:translations
```

Publishes to `lang/vendor/livewire-upload-handler/`

### Views

```bash
php artisan vendor:publish --tag=livewire-upload-handler:views
```

Publishes to `resources/views/vendor/livewire-upload-handler/`

### Themes

```bash
php artisan vendor:publish --tag=livewire-upload-handler:themes
```

Publishes themes to `resources/vendor/livewire-upload-handler/themes/`

## Environment Variables

Add to `.env` for Glide configuration:

```env
GLIDE_IMAGE_DRIVER=gd  # or 'imagick'
GLIDE_SIGN_KEY=your-random-secret-key
```

Generate a sign key:

```bash
php -r "echo bin2hex(random_bytes(32));"
```

## Next Steps

- [Configuration](configuration.md) - Configure the package
- [Basic Usage](basic-usage.md) - Start uploading files
