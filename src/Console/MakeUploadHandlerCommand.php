<?php

namespace Axn\LivewireUploadHandler\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeUploadHandlerCommand extends Command
{
    protected $signature = 'make:upload-handler
        {name : The name of the upload handler component}
        {--force : Overwrite existing files}';

    protected $description = 'Generate an upload handler component';

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $nameTerms = explode('/', str_replace('\\', '/', trim($this->argument('name'), '\\/')));

        $classRelativeName = implode('\\', array_map(fn (string $term) => Str::studly($term), $nameTerms));
        $classRelativePath = str_replace('\\', '/', $classRelativeName);
        $className = basename($classRelativePath);
        $classBasePath = app_path("Livewire/{$classRelativePath}");

        $viewRelativePath = implode('/', array_map(fn (string $term) => Str::kebab($term), $nameTerms));
        $viewName = str_replace('/', '.', $viewRelativePath);
        $viewBasePath = resource_path("views/livewire/{$viewRelativePath}");

        $this->files->ensureDirectoryExists($classBasePath);
        $this->files->ensureDirectoryExists($classBasePath.'/Concerns');
        $this->files->ensureDirectoryExists($viewBasePath);

        $this->generateClass('Item.stub', "$classBasePath/Item.php", [
            '{{classRelativeName}}' => $classRelativeName,
            '{{className}}' => $className,
            '{{viewName}}' => $viewName,
        ]);

        $this->generateClass('Group.stub', "$classBasePath/Group.php", [
            '{{classRelativeName}}' => $classRelativeName,
            '{{className}}' => $className,
            '{{viewName}}' => $viewName,
        ]);

        $this->generateClass('Common.stub', "$classBasePath/Concerns/{$className}Common.php", [
            '{{classRelativeName}}' => $classRelativeName,
            '{{className}}' => $className,
        ]);

        $this->copyView('item.blade.php', "$viewBasePath/item.blade.php");

        $this->copyView('group.blade.php', "$viewBasePath/group.blade.php");

        $this->info("Upload handler [$classRelativeName] generated successfully.");

        return Command::SUCCESS;
    }

    protected function generateClass(string $stubName, string $targetPath, array $replacements): void
    {
        if ($this->files->exists($targetPath) && ! $this->option('force')) {
            $this->warn("Skipped (exists): $targetPath");

            return;
        }

        $stub = $this->getStub($stubName);

        foreach ($replacements as $key => $value) {
            $stub = str_replace($key, $value, $stub);
        }

        $this->files->put($targetPath, $stub);
    }

    protected function copyView(string $viewName, string $targetPath): void
    {
        if ($this->files->exists($targetPath) && ! $this->option('force')) {
            $this->warn("Skipped (exists): $targetPath");

            return;
        }

        $view = $this->getView($viewName);

        $this->files->put($targetPath, $view);
    }

    protected function getStub(string $file): string
    {
        return $this->files->get(__DIR__."/../../resources/stubs/{$file}");
    }

    protected function getView(string $file): string
    {
        return $this->files->get(__DIR__."/../../resources/views/{$file}");
    }
}
