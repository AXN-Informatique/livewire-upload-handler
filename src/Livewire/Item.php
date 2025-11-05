<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire;

use Axn\LivewireUploadHandler\Enums\FileType;
use Axn\LivewireUploadHandler\Exceptions\ActionNotHandledException;
use Axn\LivewireUploadHandler\GlideServerFactory;
use Axn\LivewireUploadHandler\Livewire\Concerns\HasThemes;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
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
    use HasThemes;
    use WithFileUploads;

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
    public private(set) bool $hasFile = false;

    #[Locked]
    public array $acceptsMimeTypes = [];

    #[Locked]
    public ?int $maxFileSize = null;

    #[Locked]
    public array $compressorjsSettings = [];

    #[Locked]
    public array $glidePreviewSettings = [];

    #[Locked]
    public bool $previewImage = false;

    #[Locked]
    public bool $autoSave = false;

    #[Locked]
    public bool $onlyUpload = false;

    #[Locked]
    public bool $sortable = false;

    public function mount(): void
    {
        if ($this->glidePreviewSettings === []) {
            $this->glidePreviewSettings = config('livewire-upload-handler.glide_preview_settings');
        }

        if (old() !== []) {
            $oldData = old(str_arr_to_dot($this->inputBaseName));

            if (isset($oldData['tmpName'])) {
                $this->uploadedFile = TemporaryUploadedFile::createFromLivewire($oldData['tmpName']);
                unset($oldData['tmpName']);
            }

            $this->itemData = $oldData;
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
            inputBaseName: $this->inputBaseNameWithoutItemId,
            tmpName: $uploadedFile->getFilename(),
        );
    }

    /**
     * Save the uploaded file to permanent storage.
     * Must be implemented in child classes.
     *
     * @throws ActionNotHandledException
     */
    protected function saveUploadedFile(TemporaryUploadedFile $uploadedFile): void
    {
        throw ActionNotHandledException::saveUploadedFile(static::class);
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
        $this->dispatch(
            'livewire-upload-handler:canceled',
            inputBaseName: $this->inputBaseNameWithoutItemId,
            tmpName: $this->uploadedFile->getFilename(),
        );

        $this->uploadedFile->delete();
        $this->uploadedFile = null;
    }

    /**
     * Delete a permanently saved file.
     * Must be implemented in child classes.
     *
     * @throws ActionNotHandledException
     */
    public function deleteSavedFile(): void
    {
        throw ActionNotHandledException::deleteSavedFile(static::class);
    }

    public function downloadUploadedFile(): Response
    {
        return Storage::disk(FileUploadConfiguration::disk())
            ->download(
                path: FileUploadConfiguration::directory().'/'.$this->uploadedFile->getFilename(),
                name: $this->uploadedFile->getClientOriginalName()
            );
    }

    /**
     * Download a permanently saved file.
     * Must be implemented in child classes.
     *
     * @throws ActionNotHandledException
     */
    public function downloadSavedFile(): Response
    {
        throw ActionNotHandledException::downloadSavedFile(static::class);
    }

    public function downloadFile(): Response
    {
        return $this->uploadedFile instanceof TemporaryUploadedFile
            ? $this->downloadUploadedFile()
            : $this->downloadSavedFile();
    }

    public function render(): View
    {
        $this->hasFile = $this->uploadedFile instanceof TemporaryUploadedFile || $this->hasSavedFile();

        return view($this->viewName());
    }

    protected function viewName(): string
    {
        return 'livewire-upload-handler::item';
    }

    protected function hasSavedFile(): bool
    {
        return false;
    }

    // PHP 8.4 Property Hooks
    #[Computed]
    protected function fileExists(): bool
    {
        return $this->uploadedFile instanceof TemporaryUploadedFile
            ? $this->uploadedFile->exists()
            : $this->savedFileExists();
    }

    protected function savedFileExists(): bool
    {
        return false;
    }

    #[Computed]
    protected function fileId(): ?string
    {
        return $this->uploadedFile instanceof TemporaryUploadedFile
            ? null
            : $this->savedFileId();
    }

    protected function savedFileId(): ?string
    {
        return null;
    }

    #[Computed]
    protected function fileName(): ?string
    {
        return $this->uploadedFile instanceof TemporaryUploadedFile
            ? $this->uploadedFile->getClientOriginalName()
            : $this->savedFileName();
    }

    protected function savedFileName(): ?string
    {
        return null;
    }

    #[Computed]
    protected function imagePreviewUrl(): ?string
    {
        if (! $this->hasFile || ! $this->previewImage) {
            return null;
        }

        if (! $this->uploadedFile instanceof TemporaryUploadedFile) {
            return $this->savedImagePreviewUrl();
        }

        $mimeType = $this->uploadedFile->getMimeType();
        $fileType = FileType::fromMimeType($mimeType);

        if (! $fileType->isImage()) {
            return null;
        }

        return GlideServerFactory::forDisk(FileUploadConfiguration::disk())
            ->url(
                FileUploadConfiguration::directory().'/'.$this->uploadedFile->getFilename(),
                $this->glidePreviewSettings,
            );
    }

    protected function savedImagePreviewUrl(): ?string
    {
        return null;
    }

    #[Computed]
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
