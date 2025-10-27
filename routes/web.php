<?php

use Axn\LivewireUploadHandler\Controllers\AssetsController;
use Axn\LivewireUploadHandler\Controllers\GlideController;
use Illuminate\Support\Facades\Route;

Route::get(
    '/livewire-upload-handler/assets/{fileName}',
    AssetsController::class
);

Route::get(
    config('livewire-upload-handler.glide_base_url').'/{disk}/{path}',
    GlideController::class
)
    ->where('disk', collect(config('filesystems.disks'))->keys()->implode('|'))
    ->where('path', '(.*)');
