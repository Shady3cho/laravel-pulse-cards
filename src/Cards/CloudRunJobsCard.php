<?php

namespace LangtonMwanza\PulseDevops\Cards;

use Exception;
use Illuminate\Support\Facades\Gate;
use LangtonMwanza\PulseDevops\Services\CloudRunService;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class CloudRunJobsCard extends Component
{
    public int|string $cols = 2;

    public int|string $rows = 1;

    public string $class = '';

    public int $jobCount = 0;

    public ?array $lastExecution = null;

    public bool $configured = false;

    public ?string $error = null;

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $service = new CloudRunService();
        $this->configured = $service->isConfigured();

        if (! $this->configured) {
            return;
        }

        try {
            $jobs = $service->listJobs();
            $this->jobCount = count($jobs);

            $latest = collect($jobs)
                ->filter(fn ($job) => $job['last_execution'] !== null)
                ->sortByDesc(fn ($job) => $job['last_execution']['create_time'] ?? '')
                ->first();

            $this->lastExecution = $latest['last_execution'] ?? null;
            $this->error = null;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function render()
    {
        $authorized = Gate::allows('viewPulseDevops');
        $enabled = config('pulse-devops.cards.cloud_run_jobs', true);

        return view('pulse-devops::cards.cloud-run-jobs', [
            'authorized' => $authorized,
            'enabled' => $enabled,
        ]);
    }
}
