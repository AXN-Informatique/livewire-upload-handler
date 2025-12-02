# Customization

## Themes

Publish themes:

```bash
php artisan vendor:publish --tag=livewire-upload-handler:themes
```

### CSS Classes Theme

Default: `bootstrap-5`

Create custom theme in `resources/vendor/livewire-upload-handler/themes/css-classes/my-theme.php`:

```php
<?php

return [
    'error' => 'alert alert-danger lh-1 mt-2',
    // ... see bootstrap-5.php for full list
];
```

Set in config:

```php
'theme' => 'my-theme',
```

Or per component:

```blade
<livewire:upload-handler.item theme="my-theme" />
```

### Icons Theme

Default: `fontawesome-7`

Create custom theme in `resources/vendor/livewire-upload-handler/themes/icons/my-icons.php`:

```php
<?php

return [
    'add' => '<svg>...</svg>',
    'replace' => '<svg>...</svg>',
    'delete' => '<svg>...</svg>',
    'download' => '<svg>...</svg>',
    'sort' => '<svg>...</svg>',
];
```

Set in config or per component:

```blade
<livewire:upload-handler.item iconsTheme="my-icons" />
```

## Views

Publish views:

```bash
php artisan vendor:publish --tag=livewire-upload-handler:views
```

Modify in `resources/views/vendor/livewire-upload-handler/`:

- `group.blade.php` - Group component
- `group/actions/*` - Actions buttons for group
- `group/warnings/*` - Warnings messages for group
- `item.blade.php` - Item component
- `item/actions/*` - Actions buttons for item
- `item/warnings/*` - Warnings messages for item
- `item/filename.blade.php` - File name with download link
- `item/uploading.blade.php` - Uploading file info and progress bar
- `errors.blade.php` - Validation errors
- `components/` - View components, eg. dropzone

### Custom Progress View

Override per theme in `resources/views/vendor/livewire-upload-handler/themes/my-theme/progress.blade.php`.

## Translations

Publish translations:

```bash
php artisan vendor:publish --tag=livewire-upload-handler:translations
```

Modify in `lang/vendor/livewire-upload-handler/{locale}/`:

- `actions.php` - Button labels
- `errors.php` - Error messages
- `messages.php` - Info messages

Add new language:

```php
// lang/vendor/livewire-upload-handler/es/actions.php
return [
    'add' => 'Añadir',
    'replace' => 'Reemplazar',
    'delete' => 'Eliminar',
    'undelete' => 'Deshacer',
    'cancel' => 'Cancelar',
];
```

## Custom Dropzone

```blade
<x-livewire-upload-handler-dropzone
    class="border-2 border-dashed rounded p-4"
    overlay-class="bg-secondary opacity-25"
>
    <!-- Content wrapped in dropzone -->
</x-livewire-upload-handler-dropzone>
```

## Next Steps

- [Advanced Usage](advanced-usage.md) - Extend components
