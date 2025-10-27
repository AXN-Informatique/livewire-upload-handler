<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Controllers;

use Axn\LivewireUploadHandler\Enums\AssetType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetsController
{
    public function __invoke(string $fileName): BinaryFileResponse
    {
        $filePath = __DIR__.'/../../dist/'.$fileName;
        $assetType = AssetType::fromFilename($fileName);

        if ($assetType === null || ! file_exists($filePath)) {
            abort(404);
        }

        return response()->file($filePath, [
            'Content-Type' => $assetType->withCharset(),
        ]);
    }
}
