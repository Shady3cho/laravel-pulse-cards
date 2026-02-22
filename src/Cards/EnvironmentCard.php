<?php

namespace LangtonMwanza\PulseDevops\Cards;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class EnvironmentCard extends Component
{
    public int|string $cols = 6;

    public int|string $rows = 1;

    public string $class = '';

    public array $info = [];

    public ?string $error = null;

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        try {
            $dbConnected = false;

            try {
                DB::connection()->getPdo();
                $dbConnected = true;
            } catch (Exception) {
                // Database not reachable
            }

            $this->info = [
                'app_name' => config('app.name', 'Laravel'),
                'environment' => app()->environment(),
                'debug' => config('app.debug', false),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'db_driver' => config('database.default'),
                'db_name' => config('database.connections.'.config('database.default').'.database'),
                'db_connected' => $dbConnected,
                'cache_driver' => config('cache.default'),
                'session_driver' => config('session.driver'),
                'filesystem_disk' => config('filesystems.default'),
                'mail_driver' => config('mail.default'),
                'mail_from' => config('mail.from.address'),
                'queue_driver' => config('queue.default'),
            ];

            $this->error = null;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function render()
    {
        $authorized = Gate::allows('viewPulseDevops');
        $enabled = config('pulse-devops.cards.environment', true);

        return view('pulse-devops::cards.environment', [
            'authorized' => $authorized,
            'enabled' => $enabled,
        ]);
    }
}
