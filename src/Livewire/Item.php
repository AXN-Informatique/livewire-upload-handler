<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire;

use Axn\LivewireUploadHandler\Enums\FileType;
use Axn\LivewireUploadHandler\Exceptions\MethodNotImplementedException;
use Axn\LivewireUploadHandler\GlideServerFactory;
use Axn\LivewireUploadHandler\Livewire\Concerns\Common;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[Isolate]
class Item extends Component
{
    use Common;
    use WithFileUploads;

    public ?int $uploadingFileSize = null;

    public bool $hasErrorOnUpload = false;

    public ?TemporaryUploadedFile $chunkFile = null;

    #[Locked]
    public string $itemId;

    #[Modelable]
    public ?array $itemData = [];

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
    public ?string $savedFileDisk = null;

    #[Locked]
    public ?string $savedFilePath = null;

    public function mount(): void
    {
        if ($this->onlyUpload) {
            return;
        }

        $old = $this->old();

        if (isset($old['tmpName'])) {
            $this->uploadedFile = TemporaryUploadedFile::createFromLivewire($old['tmpName']);
        }

        if (! $this->attachedToGroup) {
            $this->initItem($old);
        }
    }

    protected function initialEntity()
    {
        return null;
    }

    protected function initItem(array $old = []): void
    {
        $entity = $this->initialEntity();

        $this->itemData = $this->initialItemData($old, $entity);

        foreach ($this->initialItemParams($entity) as $property => $value) {
            $this->{$property} = $value;
        }
    }

    #[On('livewire-upload-handler:refresh')]
    public function refreshItem(?string $inputBaseName = null): void
    {
        if ($this->attachedToGroup) {
            return;
        }

        if ($inputBaseName !== null && $inputBaseName !== $this->inputBaseName) {
            return;
        }

        $this->uploadedFile = null;

        $this->initItem();
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
                    .($this->maxFileSize > 0 ? '|max:'.$this->maxFileSize : ''),
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
            $this->itemData['tmpName'] = $uploadedFile->getFilename();
        }

        $this->dispatch(
            'luh-uploaded',
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

    /**
     * Called when upload is canceled before finished.
     * Delete the partially uploaded file.
     */
    public function deleteUploadingFile(): void
    {
        if ($this->uploadingFileName === null) {
            return;
        }

        TemporaryUploadedFile::createFromLivewire($this->uploadingFileName)->delete();

        $this->uploadingFileSize = null;
        $this->uploadingFileName = null;
    }

    /**
     * Called when upload is canceled after finished.
     * Delete the completely uploaded file.
     */
    public function deleteUploadedFile(): void
    {
        if (! $this->hasUploadedFile()) {
            return;
        }

        $this->dispatch(
            'luh-canceled',
            inputBaseName: $this->inputBaseNameWithoutItemId(),
            tmpName: $this->uploadedFile->getFilename(),
        );

        $this->uploadedFile->delete();
        $this->uploadedFile = null;

        unset($this->itemData['tmpName']);
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
        return $this->savedFileDisk !== null && $this->savedFilePath !== null;
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
            : $this->savedFileDisk;
    }

    protected function filePath(): ?string
    {
        if (! $this->hasFile()) {
            return null;
        }

        return $this->hasUploadedFile()
            ? FileUploadConfiguration::directory().'/'.$this->uploadedFile->getFilename()
            : $this->savedFilePath;
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
        return basename($this->savedFilePath);
    }

    protected function fileExists(): bool
    {
        if (! $this->hasFile()) {
            return false;
        }

        return Storage::disk($this->fileDisk())
            ->exists($this->filePath());
    }

    protected function fileSize(): ?int
    {
        if (! $this->fileExists()) {
            return null;
        }

        return Storage::disk($this->fileDisk())
            ->size($this->filePath());
    }

    protected function fileMimeType(): ?string
    {
        if (! $this->fileExists()) {
            return null;
        }

        return Storage::disk($this->fileDisk())
            ->mimeType($this->filePath());
    }

    protected function fileType(): ?FileType
    {
        if (! $this->fileExists()) {
            return null;
        }

        return FileType::fromMimeType($this->fileMimeType());
    }

    protected function glideUrl(array $params = []): ?string
    {
        if (! $this->fileExists()) {
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
