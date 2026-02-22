<?php

namespace LangtonMwanza\PulseDevops\Cards;

use Exception;
use Illuminate\Support\Facades\Gate;
use LangtonMwanza\PulseDevops\Services\MigrationService;
use Livewire\Component;

class MigrationsCard extends Component
{
    public int|string $cols = 2;

    public int|string $rows = 1;

    public string $class = '';

    public array $status = [];

    public ?string $error = null;

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        try {
            $service = new MigrationService();
            $this->status = $service->getStatus();
            $this->error = null;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function render()
    {
        $authorized = Gate::allows('viewPulseDevops');
        $enabled = config('pulse-devops.cards.migrations', true);

        return view('pulse-devops::cards.migrations', [
            'authorized' => $authorized,
            'enabled' => $enabled,
        ]);
    }
}
