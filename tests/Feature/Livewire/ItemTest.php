<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Tests\Feature\Livewire;

use Axn\LivewireUploadHandler\Exceptions\MethodNotImplementedException;
use Axn\LivewireUploadHandler\Livewire\Item;
use Axn\LivewireUploadHandler\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Exceptions;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Livewire;

class ItemTest extends TestCase
{
    public function test_single_chunk_upload_keeps_assembled_file_and_dispatches_uploaded_event(): void
    {
        $component = Livewire::test(Item::class, ['inputBaseName' => 'report'])
            ->set('uploadingFileSize', 11)
            ->upload('chunkFile', [UploadedFile::fake()->createWithContent('report.txt', 'hello world')]);

        $tmpName = $component->get('itemData')['tmpName'];
        $assembledFile = TemporaryUploadedFile::createFromLivewire($tmpName);

        $this->assertSame('hello world', $assembledFile->get());
        $this->assertSame('report.txt', $assembledFile->getClientOriginalName());
        $component
            ->assertSet('uploadedFile', fn (?TemporaryUploadedFile $file): bool => $file?->getFilename() === $tmpName)
            ->assertSet('hasErrorOnUpload', false)
            ->assertDispatched('luh-uploaded', inputBaseName: 'report', tmpName: $tmpName);
    }

    public function test_multi_chunk_upload_appends_chunks_in_order_and_dispatches_uploaded_event_once_complete(): void
    {
        $component = Livewire::test(Item::class)
            ->set('uploadingFileSize', 11)
            ->upload('chunkFile', [UploadedFile::fake()->createWithContent('report.txt', 'hello ')]);

        $component->assertNotDispatched('luh-uploaded');

        $component->upload('chunkFile', [UploadedFile::fake()->createWithContent('report.txt', 'world')]);

        $tmpName = $component->get('itemData')['tmpName'];

        $this->assertSame('hello world', TemporaryUploadedFile::createFromLivewire($tmpName)->get());
        $component->assertDispatched('luh-uploaded', tmpName: $tmpName);
    }

    public function test_multi_chunk_upload_leaves_only_the_assembled_file_in_temporary_directory(): void
    {
        $component = Livewire::test(Item::class)
            ->set('uploadingFileSize', 11)
            ->upload('chunkFile', [UploadedFile::fake()->createWithContent('report.txt', 'hello ')])
            ->upload('chunkFile', [UploadedFile::fake()->createWithContent('report.txt', 'world')]);

        $this->assertSame(
            [FileUploadConfiguration::path($component->get('itemData')['tmpName'])],
            $this->temporaryDirectoryFiles(),
        );
    }

    public function test_upload_with_unaccepted_mime_type_is_rejected_and_deleted(): void
    {
        $component = Livewire::test(Item::class, ['acceptsMimeTypes' => ['image/png']])
            ->set('uploadingFileSize', 11)
            ->upload('chunkFile', [UploadedFile::fake()->createWithContent('report.txt', 'hello world')]);

        $component
            ->assertHasErrors('uploadedFile')
            ->assertSet('uploadedFile', null)
            ->assertNotDispatched('luh-uploaded');
        $this->assertSame([], $this->temporaryDirectoryFiles());
    }

    public function test_upload_exceeding_max_file_size_is_rejected_and_deleted(): void
    {
        $component = Livewire::test(Item::class, ['maxFileSize' => 1])
            ->set('uploadingFileSize', 2048)
            ->upload('chunkFile', [UploadedFile::fake()->createWithContent('report.txt', str_repeat('a', 2048))]);

        $component
            ->assertHasErrors('uploadedFile')
            ->assertNotDispatched('luh-uploaded');
        $this->assertSame([], $this->temporaryDirectoryFiles());
    }

    public function test_failure_while_finishing_upload_flags_upload_error_and_deletes_assembled_file(): void
    {
        $component = Livewire::test(Item::class, ['autoSave' => true])
            ->set('uploadingFileSize', 11)
            ->upload('chunkFile', [UploadedFile::fake()->createWithContent('report.txt', 'hello world')]);

        $component
            ->assertSet('hasErrorOnUpload', true)
            ->assertSet('uploadingFileSize', null)
            ->assertSet('uploadingFileName', null);
        $this->assertSame([], $this->temporaryDirectoryFiles());
    }

    public function test_failure_while_finishing_upload_is_reported_to_exception_handler(): void
    {
        Exceptions::fake();

        Livewire::test(Item::class, ['autoSave' => true])
            ->set('uploadingFileSize', 11)
            ->upload('chunkFile', [UploadedFile::fake()->createWithContent('report.txt', 'hello world')]);

        Exceptions::assertReported(MethodNotImplementedException::class);
    }

    public function test_only_upload_mode_dispatches_uploaded_event_without_keeping_the_file_in_component(): void
    {
        $component = Livewire::test(Item::class, ['onlyUpload' => true])
            ->set('uploadingFileSize', 11)
            ->upload('chunkFile', [UploadedFile::fake()->createWithContent('report.txt', 'hello world')]);

        $component
            ->assertSet('uploadedFile', null)
            ->assertDispatched('luh-uploaded');
        $this->assertArrayNotHasKey('tmpName', $component->get('itemData'));
    }

    public function test_deleting_uploaded_file_removes_it_and_dispatches_canceled_event(): void
    {
        $component = Livewire::test(Item::class, ['inputBaseName' => 'report'])
            ->set('uploadingFileSize', 11)
            ->upload('chunkFile', [UploadedFile::fake()->createWithContent('report.txt', 'hello world')]);
        $tmpName = $component->get('itemData')['tmpName'];

        $component->call('deleteUploadedFile');

        $component
            ->assertSet('uploadedFile', null)
            ->assertDispatched('luh-canceled', inputBaseName: 'report', tmpName: $tmpName);
        $this->assertArrayNotHasKey('tmpName', $component->get('itemData'));
        $this->assertSame([], $this->temporaryDirectoryFiles());
    }

    public function test_canceling_upload_in_progress_deletes_partially_assembled_file(): void
    {
        $component = Livewire::test(Item::class)
            ->set('uploadingFileSize', 11)
            ->upload('chunkFile', [UploadedFile::fake()->createWithContent('report.txt', 'hello ')]);

        $component->call('deleteUploadingFile');

        $component
            ->assertSet('uploadingFileSize', null)
            ->assertSet('uploadingFileName', null);
        $this->assertSame([], $this->temporaryDirectoryFiles());
    }

    public function test_refresh_event_for_its_input_resets_uploaded_file(): void
    {
        $component = Livewire::test(Item::class, ['inputBaseName' => 'report'])
            ->set('uploadingFileSize', 11)
            ->upload('chunkFile', [UploadedFile::fake()->createWithContent('report.txt', 'hello world')]);

        $component->dispatch('livewire-upload-handler:refresh', inputBaseName: 'report');

        $component
            ->assertSet('uploadedFile', null)
            ->assertSet('itemData.tmpName', null);
    }

    public function test_refresh_event_for_another_input_keeps_uploaded_file(): void
    {
        $component = Livewire::test(Item::class, ['inputBaseName' => 'report'])
            ->set('uploadingFileSize', 11)
            ->upload('chunkFile', [UploadedFile::fake()->createWithContent('report.txt', 'hello world')]);
        $tmpName = $component->get('itemData')['tmpName'];

        $component->dispatch('livewire-upload-handler:refresh', inputBaseName: 'other');

        $component->assertSet('itemData.tmpName', $tmpName);
    }
}
