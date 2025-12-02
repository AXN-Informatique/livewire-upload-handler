# Customization

## Themes

### CSS Classes Theme

Default: `bootstrap-5`

Create custom theme in `resources/vendor/livewire-upload-handler/themes/css-classes/my-theme.php`:

```php
<?php

return [
    'dropzone' => 'border-2 border-dashed rounded p-4',
    'item' => 'flex items-center gap-2 p-2',
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
    'upload' => '<svg>...</svg>',
    'download' => '<svg>...</svg>',
    'delete' => '<svg>...</svg>',
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
- `item.blade.php` - Item component
- `group/add.blade.php` - Add button
- `item/actions.blade.php` - Action buttons
- `item/body.blade.php` - File display
- `item/progress.blade.php` - Progress bar

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
<x-livewire-upload-handler-dropzone>
    <!-- Custom content -->
</x-livewire-upload-handler-dropzone>
```

## Next Steps

- [Advanced Usage](advanced-usage.md) - Extend components
