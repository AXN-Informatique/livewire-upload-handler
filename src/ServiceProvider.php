<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler;

use Axn\LivewireUploadHandler\Components\Dropzone;
use Axn\LivewireUploadHandler\Livewire\Group;
use Axn\LivewireUploadHandler\Livewire\Item;
use Axn\LivewireUploadHandler\Livewire\MediaGroup;
use Axn\LivewireUploadHandler\Livewire\MediaItem;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;

class ServiceProvider extends BaseServiceProvider
{
    private string $basePath = '';

    public function register(): void
    {
        $this->basePath = __DIR__.'/../';

        $this->registerConfig();
    }

    public function boot(): void
    {
        $this->loadRoutesFrom($this->basePath.'routes/web.php');

        $this->loadTranslationsFrom($this->basePath.'lang', 'livewire-upload-handler');

        $this->loadViewsFrom([
            $this->basePath.'resources/views',
        ], 'livewire-upload-handler');

        $this->registerBladeDirectives();

        $this->registerLivewireComponents();

        $this->registerBladeComponents();

        if ($this->app->runningInConsole()) {
            $this->commands([
                // ...
            ]);

            $this->configurePublishing();
        }
    }

    private function registerConfig(): void
    {
        $this->mergeConfigFrom(
            $this->basePath.'config/livewire-upload-handler.php',
            'livewire-upload-handler'
        );
    }

    private function registerBladeDirectives(): void
    {
        $config = $this->app['config']->get('livewire-upload-handler');

        $manifest = json_decode(
            file_get_contents($this->basePath.'dist/manifest.json'),
            associative: true,
            flags: JSON_THROW_ON_ERROR
        );

        $assetsBaseUrl = '/livewire-upload-handler/assets/';
        $debugMode = config('app.debug');
        $scriptsUrl = $assetsBaseUrl.$manifest['scripts'.($debugMode ? '' : '.min').'.js'];
        $stylesUrl = $assetsBaseUrl.$manifest['styles.css'];

        $scriptsParams = 'window.livewireUploadHandlerParams = '.json_encode([
            'chunkSize' => $config['chunk_size'],
            'compressorjsVar' => $config['compressorjs_var'],
            'sortablejsVar' => $config['sortablejs_var'],
            'maxFilesNumberReachedMessage' => __('livewire-upload-handler::errors.max_files_number_reached'),
            'invalidFileTypeErrorMessage' => __('livewire-upload-handler::errors.invalid_file_type'),
            'fileTooLoudErrorMessage' => __('livewire-upload-handler::errors.file_too_loud'),
            'uploadErrorMessage' => __('livewire-upload-handler::errors.upload'),
        ], JSON_THROW_ON_ERROR);

        Blade::directive('livewireUploadHandlerScripts', fn (): string => <<<HTML
            <script>{$scriptsParams}</script>
            <script src="{$scriptsUrl}"></script>
        HTML);

        Blade::directive('livewireUploadHandlerStyles', fn (): string => <<<HTML
            <link href="{$stylesUrl}" rel="stylesheet">
        HTML);
    }

    private function registerLivewireComponents(): void
    {
        Livewire::component('upload-handler.group', Group::class);
        Livewire::component('upload-handler.item', Item::class);

        Livewire::component('upload-handler.media-group', MediaGroup::class);
        Livewire::component('upload-handler.media-item', MediaItem::class);
    }

    private function registerBladeComponents(): void
    {
        $this->callAfterResolving(BladeCompiler::class, function (BladeCompiler $blade): void {
            $blade->component(Dropzone::class, 'dropzone', 'livewire-upload-handler');
        });
    }

    private function configurePublishing(): void
    {
        // config
        $this->publishes([
            $this->basePath.'config/livewire-upload-handler.stub' => $this->app->configPath('livewire-upload-handler.php'),
        ], 'livewire-upload-handler:config');

        // translations
        $this->publishes([
            $this->basePath.'lang/' => $this->app->langPath('vendor/livewire-upload-handler'),
        ], 'livewire-upload-handler:translations');

        // views
        $this->publishes(
            $this->buildViewsPublishArray(),
            'livewire-upload-handler:views'
        );

        // themes
        $this->publishes([
            $this->basePath.'resources/themes' => $this->app->resourcePath('vendor/livewire-upload-handler/themes'),
            $this->basePath.'resources/views/themes' => $this->app->resourcePath('views/vendor/livewire-upload-handler/themes'),
        ], 'livewire-upload-handler:themes');
    }

    /**
     * @return array<string, string>
     */
    private function buildViewsPublishArray(): array
    {
        return collect([
            'components/',
            'group/actions/',
            'group/warnings/',
            'item/actions/',
            'item/warnings/',
            'item/filename.blade.php',
            'item/uploading.blade.php',
            'item.blade.php',
            'group.blade.php',
            'errors.blade.php',
        ])
            ->mapWithKeys(fn (string $viewPath): array => [
                $this->basePath.'resources/views/'.$viewPath => $this->app->resourcePath('views/vendor/livewire-upload-handler/'.$viewPath),
            ])
            ->all();
    }
}
