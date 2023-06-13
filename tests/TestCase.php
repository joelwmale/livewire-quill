<?php

namespace Joelwmale\LivewireQuill\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Facade;
use Joelwmale\LivewireQuill\LivewireQuillServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        $this->afterApplicationCreated(function () {
            $this->makeACleanSlate();
        });

        $this->beforeApplicationDestroyed(function () {
            $this->makeACleanSlate();
        });

        parent::setUp();
        Facade::setFacadeApplication(app());
    }

    public function makeACleanSlate()
    {
        Artisan::call('view:clear');
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            LivewireQuillServiceProvider::class,
        ];
    }

        protected function getEnvironmentSetUp($app)
        {
            $app['config']->set('view.paths', [
                __DIR__.'/../views',
                resource_path('views'),
            ]);

            $app['config']->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');
        }
}
