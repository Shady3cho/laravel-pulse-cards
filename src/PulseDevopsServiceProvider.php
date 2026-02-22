<?php

namespace LangtonMwanza\PulseDevops;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use LangtonMwanza\PulseDevops\Cards\CloudRunJobsCard;
use LangtonMwanza\PulseDevops\Cards\DatabaseTablesCard;
use LangtonMwanza\PulseDevops\Cards\EnvironmentCard;
use LangtonMwanza\PulseDevops\Cards\GcpSecretsCard;
use LangtonMwanza\PulseDevops\Cards\MigrationsCard;
use LangtonMwanza\PulseDevops\Cards\QueueHealthCard;
use Livewire\Livewire;

class PulseDevopsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/pulse-devops.php', 'pulse-devops');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'pulse-devops');
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->publishes([
            __DIR__.'/../stubs/pulse-devops.php' => config_path('pulse-devops.php'),
        ], 'pulse-devops-config');

        $this->registerLivewireComponents();
        $this->registerGate();
    }

    protected function registerLivewireComponents(): void
    {
        if (! class_exists(Livewire::class)) {
            return;
        }

        Livewire::component('pulse-devops.migrations', MigrationsCard::class);
        Livewire::component('pulse-devops.database-tables', DatabaseTablesCard::class);
        Livewire::component('pulse-devops.gcp-secrets', GcpSecretsCard::class);
        Livewire::component('pulse-devops.cloud-run-jobs', CloudRunJobsCard::class);
        Livewire::component('pulse-devops.queue-health', QueueHealthCard::class);
        Livewire::component('pulse-devops.environment', EnvironmentCard::class);
    }

    protected function registerGate(): void
    {
        Gate::define('viewPulseDevops', function ($user = null) {
            if (app()->environment('local')) {
                return true;
            }

            if ($user && method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
                return true;
            }

            $allowedEmails = config('pulse-devops.allowed_emails', []);

            return $user && in_array($user->email, $allowedEmails);
        });
    }
}
