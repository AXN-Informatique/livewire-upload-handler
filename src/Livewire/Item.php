<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire;

use Axn\LivewireUploadHandler\Enums\FileType;
use Axn\LivewireUploadHandler\Exceptions\MethodNotImplementedException;
use Axn\LivewireUploadHandler\GlideServerFactory;
use Axn\LivewireUploadHandler\Livewire\Concerns\HasThemes;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use function Axn\LivewireUploadHandler\str_arr_to_dot;

#[Isolate]
class Item extends Component
{
    use HasThemes, WithFileUploads;

    public ?int $uploadingFileSize = null;

    public bool $hasErrorOnUpload = false;

    public ?TemporaryUploadedFile $chunkFile = null;

    #[Locked]
    public string $itemId;

    #[Modelable]
    public array $itemData = [];

    #[Locked]
    public string $inputBaseName = 'file';

    #[Locked]
    public bool $attachedToGroup = false;

    #[Locked]
    public ?int $uploadFromGroupAtIndex = null;

    #[Locked]
    public ?string $uploadingFileName = null;

    #[Locked]
    public ?TemporaryUploadedFile $uploadedFile = null;

    #[Locked]
    public array $acceptsMimeTypes = [];

    #[Locked]
    public ?int $maxFileSize = null;

    #[Locked]
    public array $compressorjsSettings = [];

    #[Locked]
    public bool $showImagePreview = false;

    #[Locked]
    public bool $autoSave = false;

    #[Locked]
    public bool $onlyUpload = false;

    public function mount(): void
    {
        if (old() !== []) {
            $oldData = old(str_arr_to_dot($this->inputBaseName));

            if (isset($oldData['tmpName'])) {
                $this->uploadedFile = TemporaryUploadedFile::createFromLivewire($oldData['tmpName']);
                unset($oldData['tmpName']);
            }

            $this->itemData = $oldData;

        } elseif ($this->itemData === []) {
            $this->itemData = [
                'id' => null,
                'deleted' => false,
            ];
        }
    }

    /**
     * Handles chunked file upload.
     *
     * @see https://fly.io/laravel-bytes/chunked-file-upload-livewire/
     */
    public function updatedChunkFile(): void
    {
        if ($this->uploadingFileSize === null) {
            return;
        }

        if ($this->uploadingFileName === null) {
            $this->uploadingFileName = TemporaryUploadedFile::generateHashNameWithOriginalNameEmbedded($this->chunkFile);
            $this->uploadedFile = null;
        }

        try {
            $this->processChunk();

        } catch (Throwable $throwable) {
            Log::error($throwable);
            $this->hasErrorOnUpload = true;

            return;
        }

        $finalFile = TemporaryUploadedFile::createFromLivewire($this->uploadingFileName);

        if ($finalFile->getSize() === $this->uploadingFileSize) {
            $this->uploadingFileSize = null;
            $this->uploadingFileName = null;

            $this->validateUploadedFile($finalFile);
            $this->uploadFinished($finalFile);
        }
    }

    /**
     * Process a single chunk of the uploaded file.
     */
    protected function processChunk(): void
    {
        $chunkContent = file_get_contents($this->chunkFile->getPathname());

        $this->chunkFile->delete();
        $this->chunkFile = null;

        $finalFilePath = TemporaryUploadedFile::createFromLivewire($this->uploadingFileName)->getPathname();
        file_put_contents($finalFilePath, $chunkContent, FILE_APPEND);
    }

    /**
     * Validate the uploaded file against MIME types and size constraints.
     */
    protected function validateUploadedFile(TemporaryUploadedFile $uploadedFile): void
    {
        try {
            Validator::make([
                'uploadedFile' => $uploadedFile,
            ], [
                'uploadedFile' => 'file'
                    .($this->acceptsMimeTypes !== [] ? '|mimetypes:'.implode(',', $this->acceptsMimeTypes) : '')
                    .($this->maxFileSize !== null ? '|max:'.$this->maxFileSize : ''),
            ])->validate();

        } catch (ValidationException $validationException) {
            $uploadedFile->delete();

            throw $validationException;
        }
    }

    /**
     * Called when file upload is complete.
     */
    protected function uploadFinished(TemporaryUploadedFile $uploadedFile): void
    {
        if ($this->autoSave) {
            $this->saveUploadedFile($uploadedFile);

            return;
        }

        if (! $this->onlyUpload) {
            $this->uploadedFile = $uploadedFile;
        }

        $this->dispatch(
            'livewire-upload-handler:uploaded',
            inputBaseName: $this->inputBaseNameWithoutItemId(),
            tmpName: $uploadedFile->getFilename(),
        );
    }

    /**
     * Save the uploaded file to permanent storage.
     * Must be implemented in child classes.
     *
     * @throws MethodNotImplementedException
     */
    protected function saveUploadedFile(TemporaryUploadedFile $uploadedFile): void
    {
        throw MethodNotImplementedException::saveUploadedFile(static::class);
    }

    public function deleteUploadingFile(): void
    {
        if ($this->uploadingFileName === null) {
            return;
        }

        TemporaryUploadedFile::createFromLivewire($this->uploadingFileName)->delete();

        $this->uploadingFileSize = null;
        $this->uploadingFileName = null;
    }

    public function deleteUploadedFile(): void
    {
        if (! $this->hasUploadedFile()) {
            return;
        }

        $this->dispatch(
            'livewire-upload-handler:canceled',
            inputBaseName: $this->inputBaseNameWithoutItemId(),
            tmpName: $this->uploadedFile->getFilename(),
        );

        $this->uploadedFile->delete();
        $this->uploadedFile = null;
    }

    /**
     * Delete a permanently saved file.
     * Must be implemented in child classes.
     *
     * @throws MethodNotImplementedException
     */
    public function deleteSavedFile(): void
    {
        throw MethodNotImplementedException::deleteSavedFile(static::class);
    }

    public function downloadFile(): Response
    {
        if (! $this->fileExists()) {
            abort(404);
        }

        return Storage::disk($this->fileDisk())
            ->download(
                path: $this->filePath(),
                name: $this->fileName(),
            );
    }

    public function render(): View
    {
        return view('livewire-upload-handler::item');
    }

    protected function hasUploadedFile(): bool
    {
        return $this->uploadedFile instanceof TemporaryUploadedFile;
    }

    protected function hasSavedFile(): bool
    {
        return $this->itemData['id'] !== null;
    }

    protected function hasFile(): bool
    {
        return $this->hasUploadedFile() || $this->hasSavedFile();
    }

    protected function fileDisk(): ?string
    {
        if (! $this->hasFile()) {
            return null;
        }

        return $this->hasUploadedFile()
            ? FileUploadConfiguration::disk()
            : $this->savedFileDisk();
    }

    protected function savedFileDisk(): string
    {
        throw MethodNotImplementedException::savedFileDisk(static::class);
    }

    protected function filePath(): ?string
    {
        if (! $this->hasFile()) {
            return null;
        }

        return $this->hasUploadedFile()
            ? FileUploadConfiguration::directory().'/'.$this->uploadedFile->getFilename()
            : $this->savedFilePath();
    }

    protected function savedFilePath(): string
    {
        throw MethodNotImplementedException::savedFilePath(static::class);
    }

    protected function fileExists(): bool
    {
        if (! $this->hasFile()) {
            return false;
        }

        return Storage::disk($this->fileDisk())
            ->exists($this->filePath());
    }

    protected function fileName(): ?string
    {
        if (! $this->hasFile()) {
            return null;
        }

        return $this->hasUploadedFile()
            ? $this->uploadedFile->getClientOriginalName()
            : $this->savedFileName();
    }

    protected function savedFileName(): string
    {
        throw MethodNotImplementedException::savedFileName(static::class);
    }

    protected function fileType(): ?FileType
    {
        if (! $this->hasFile()) {
            return null;
        }

        $mimeType = $this->hasUploadedFile()
            ? $this->uploadedFile->getMimeType()
            : $this->savedFileMimeType();

        return FileType::fromMimeType($mimeType);
    }

    protected function savedFileMimeType(): string
    {
        throw MethodNotImplementedException::savedFileMimeType(static::class);
    }

    protected function glideUrl(array $params = []): ?string
    {
        if (! $this->hasFile()) {
            return null;
        }

        return GlideServerFactory::forDisk($this->fileDisk())
            ->url(
                path: $this->filePath(),
                params: $params,
            );
    }

    protected function inputBaseNameWithoutItemId(): string
    {
        if (! $this->attachedToGroup) {
            return $this->inputBaseName;
        }

        return Str::of($this->inputBaseName)
            ->beforeLast('[')
            ->toString();
    }
}
