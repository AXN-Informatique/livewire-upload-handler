<?php

namespace Axn\LivewireUploadHandler\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetsController
{
    public function __invoke(string $fileName): BinaryFileResponse
    {
        $filePath = __DIR__.'/../../dist/'.$fileName;
        $fileType = null;

        if (str($fileName)->endsWith('.js')) {
            $fileType = 'application/javascript';

        } elseif (str($fileName)->endsWith('.css')) {
            $fileType = 'text/css';
        }

        if ($fileType === null || ! file_exists($filePath)) {
            abort(404);
        }

        return response()->file($filePath, [
            'Content-Type' => $fileType.'; charset=utf-8',
        ]);
    }
}
