<?php

namespace Joelwmale\LivewireQuill;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Joelwmale\LivewireQuill\Facades\LivewireQuill as LivewireQuillFacade;
use Joelwmale\LivewireQuill\Http\Livewire\LivewireQuill;
use Livewire\Livewire;

class LivewireQuillServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'livewire-quill');

        $this->registerPublishables();
    }

    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/livewire-quill.php', 'livewire-quill');

        // Add Livewire component
        $this->app->afterResolving(BladeCompiler::class, function () {
            if (class_exists(Livewire::class)) {
                Livewire::component('livewire-quill', LivewireQuill::class);
            }
        });

        // Register the main class to use with the facade
        $this->app->singleton('livewire-quill', function () {
            return new LivewireQuillFacade();
        });
    }

        private function registerPublishables()
        {
            if (! $this->app->runningInConsole()) {
                return;
            }

            $this->publishes([
                __DIR__.'/../config/livewire-quill.php' => config_path('livewire-quill.php'),
            ], 'livewire-quill:config');

            // Publishing the views.
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/livewire-quill'),
            ], 'livewire-quill:views');
        }
}
