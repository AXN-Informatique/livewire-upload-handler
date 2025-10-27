<?php

namespace Axn\LivewireUploadHandler\Controllers;

use Axn\LivewireUploadHandler\GlideServerFactory;
use Illuminate\Http\Request;

class GlideController
{
    public function __invoke(string $disk, string $path, Request $request)
    {
        return GlideServerFactory::forDisk($disk)
            ->imageResponse($path, $request->all());
    }
}
