<?php

declare(strict_types=1);

namespace Axn\LivewireUploadHandler\Livewire\Concerns;

use Livewire\Attributes\Computed;

trait HasThemes
{
    public ?string $theme = null;

    public ?string $iconsTheme = null;

    protected function themedViewPath(string $viewName): string
    {
        $theme = ($this->theme ?: config('livewire-upload-handler.theme')) ?: 'default';

        $themedViewPath = 'livewire-upload-handler::themes.'.$theme.'.'.$viewName;

        if (! view()->exists($themedViewPath)) {
            return 'livewire-upload-handler::themes.default.'.$viewName;
        }

        return $themedViewPath;
    }

    #[Computed]
    protected function cssClasses(): ?array
    {
        $theme = $this->theme ?: config('livewire-upload-handler.theme');

        return $this->loadTheme('css-classes', $theme);
    }

    #[Computed]
    protected function icons(): ?array
    {
        $theme = $this->iconsTheme ?: config('livewire-upload-handler.icons_theme');

        return $this->loadTheme('icons', $theme);
    }

    private function loadTheme(string $dir, ?string $name): ?array
    {
        if (! $name) {
            return null;
        }

        $path = resource_path('vendor/livewire-upload-handler/themes/'.$dir.'/'.$name.'.php');

        if (! file_exists($path)) {
            $path = __DIR__.'/../../../resources/themes/'.$dir.'/'.$name.'.php';

            if (! file_exists($path)) {
                return null;
            }
        }

        return require $path;
    }
}
