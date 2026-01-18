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

## Git Configuration (Important)

Lors de l'utilisation des uploads Livewire, deux répertoires de fichiers temporaires sont créés automatiquement :

1. **`storage/app/livewire-tmp/`** - Fichiers temporaires Livewire (créé par Livewire)
2. **`storage/app/.livewire-upload-handler-glide-cache/`** - Cache des prévisualisations Glide (créé par ce package)

**Ces répertoires ne doivent pas être versionnés dans Git** car ils contiennent des fichiers temporaires régénérés automatiquement.

### Configuration requise

Ajoutez `livewire-tmp/` dans le `.gitignore` principal de votre application :

**`storage/app/.gitignore`**

```gitignore
*
!public/
!.gitignore
livewire-tmp/
```

Créez également un `.gitignore` dédié pour le cache Glide :

**`storage/app/.livewire-upload-handler-glide-cache/.gitignore`**

```gitignore
*
!.gitignore
```

### Pourquoi ?

**`livewire-tmp/`** : Stocke temporairement les fichiers uploadés pendant le traitement. Ces fichiers sont automatiquement nettoyés par Livewire.

**`.livewire-upload-handler-glide-cache/`** : Stocke les prévisualisations d'images générées par Glide lors des uploads. Ces fichiers sont :
- Temporaires et régénérés à la demande
- Spécifiques à chaque environnement
- Inutiles dans le dépôt Git (augmentent sa taille sans raison)

### Création automatique des fichiers

Vous pouvez créer ces configurations manuellement ou utiliser cette commande :

```bash
# Ajouter livewire-tmp/ au .gitignore principal
if ! grep -q "livewire-tmp/" storage/app/.gitignore 2>/dev/null; then
    echo "livewire-tmp/" >> storage/app/.gitignore
fi

# Créer le .gitignore pour le cache Glide
mkdir -p storage/app/.livewire-upload-handler-glide-cache && \
echo "*" > storage/app/.livewire-upload-handler-glide-cache/.gitignore && \
echo "!.gitignore" >> storage/app/.livewire-upload-handler-glide-cache/.gitignore
```

## Next Steps

- [Configuration](configuration.md) - Configure the package
- [Basic Usage](basic-usage.md) - Start uploading files
