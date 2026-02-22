<?php

namespace LangtonMwanza\PulseDevops\Cards;

use Exception;
use Illuminate\Support\Facades\Gate;
use LangtonMwanza\PulseDevops\Services\DatabaseInspector;
use Livewire\Component;

class DatabaseTablesCard extends Component
{
    public int|string $cols = 2;

    public int|string $rows = 1;

    public string $class = '';

    public array $size = [];

    public array $topTables = [];

    public ?string $error = null;

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        try {
            $inspector = new DatabaseInspector();
            $this->size = $inspector->getDatabaseSize();
            $this->topTables = $inspector->getTopTables(3)->toArray();
            $this->error = null;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function render()
    {
        $authorized = Gate::allows('viewPulseDevops');
        $enabled = config('pulse-devops.cards.database_tables', true);

        return view('pulse-devops::cards.database-tables', [
            'authorized' => $authorized,
            'enabled' => $enabled,
        ]);
    }
}
