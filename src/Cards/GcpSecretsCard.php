<?php

namespace LangtonMwanza\PulseDevops\Cards;

use Exception;
use Illuminate\Support\Facades\Gate;
use LangtonMwanza\PulseDevops\Services\GcpSecretManager;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class GcpSecretsCard extends Component
{
    public int|string $cols = 2;

    public int|string $rows = 1;

    public string $class = '';

    public int $secretCount = 0;

    public bool $configured = false;

    public ?string $error = null;

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $manager = new GcpSecretManager();
        $this->configured = $manager->isConfigured();

        if (! $this->configured) {
            return;
        }

        try {
            $secrets = $manager->listSecrets();
            $this->secretCount = count($secrets);
            $this->error = null;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function render()
    {
        $authorized = Gate::allows('viewPulseDevops');
        $enabled = config('pulse-devops.cards.gcp_secrets', true);

        return view('pulse-devops::cards.gcp-secrets', [
            'authorized' => $authorized,
            'enabled' => $enabled,
        ]);
    }
}
