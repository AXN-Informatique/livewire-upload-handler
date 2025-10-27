<?php

declare(strict_types=1);

use function Axn\LivewireUploadHandler\bytes_to_int;

return [

    'theme' => 'bootstrap-5',

    'icons_theme' => 'fontawesome-7',

    'compressorjs_var' => 'window.Compressor',

    'sortablejs_var' => 'window.Sortable',

    'chunk_size' => bytes_to_int(ini_get('upload_max_filesize')),

    'glide_max_image_size' => 2000 * 2000,

    'glide_image_driver' => env('GLIDE_IMAGE_DRIVER', 'gd'),

    'glide_sign_key' => env('GLIDE_SIGN_KEY'),

    'glide_base_url' => '/livewire-upload-handler/glide',

    'glide_preview_settings' => [
        'w' => 70,
        'h' => 70,
        'fit' => 'crop',
    ],

];
