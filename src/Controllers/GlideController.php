<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Controllers;

use Axn\LivewireUploadHandler\GlideServerFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GlideController
{
    public function __invoke(string $disk, string $path, Request $request): StreamedResponse
    {
        return GlideServerFactory::forDisk($disk)
            ->imageResponse($path, $request->all());
    }
}
