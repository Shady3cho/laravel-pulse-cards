<?php

namespace LangtonMwanza\PulseDevops\Cards;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class QueueHealthCard extends Component
{
    public int|string $cols = 2;

    public int|string $rows = 1;

    public string $class = '';

    public string $driver = 'unknown';

    public int $pendingJobs = 0;

    public int $failedJobs = 0;

    public ?string $error = null;

    public ?string $actionMessage = null;

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        try {
            $this->driver = config('queue.default', 'sync');
            $this->pendingJobs = Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0;
            $this->failedJobs = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
            $this->error = null;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function retryAll(): void
    {
        try {
            Artisan::call('queue:retry', ['id' => ['all']]);
            $this->actionMessage = 'All failed jobs have been pushed back onto the queue.';
            $this->loadData();
        } catch (Exception $e) {
            $this->actionMessage = 'Error: '.$e->getMessage();
        }
    }

    public function flushFailed(): void
    {
        try {
            Artisan::call('queue:flush');
            $this->actionMessage = 'All failed jobs have been deleted.';
            $this->loadData();
        } catch (Exception $e) {
            $this->actionMessage = 'Error: '.$e->getMessage();
        }
    }

    public function render()
    {
        $authorized = Gate::allows('viewPulseDevops');
        $enabled = config('pulse-devops.cards.queue_health', true);

        return view('pulse-devops::cards.queue-health', [
            'authorized' => $authorized,
            'enabled' => $enabled,
        ]);
    }
}
