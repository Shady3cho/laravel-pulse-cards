<?php

namespace LangtonMwanza\PulseDevops\Tests;

use LangtonMwanza\PulseDevops\PulseDevopsServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            PulseDevopsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        $app['config']->set('pulse-devops.authorize', true);
        $app['config']->set('pulse-devops.gcp.project_id', 'test-project');
        $app['config']->set('pulse-devops.gcp.region', 'us-central1');
    }
}
