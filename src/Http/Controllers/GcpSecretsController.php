<?php

namespace LangtonMwanza\PulseDevops\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LangtonMwanza\PulseDevops\Services\GcpSecretManager;

class GcpSecretsController extends Controller
{
    public function __construct(
        protected GcpSecretManager $secretManager,
    ) {}

    public function index()
    {
        if (! config('pulse-devops.cards.gcp_secrets', true)) {
            abort(404);
        }

        $configured = $this->secretManager->isConfigured();
        $secrets = [];
        $error = null;

        if ($configured) {
            try {
                $secrets = $this->secretManager->listSecrets();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        return view('pulse-devops::pages.gcp-secrets', compact('configured', 'secrets', 'error'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|regex:/^[a-zA-Z0-9_-]+$/',
            'value' => 'required|string',
        ]);

        try {
            $this->secretManager->createSecret($request->input('name'), $request->input('value'));

            return redirect()->route('pulse-devops.secrets')
                ->with('status', 'success')
                ->with('message', "Secret '{$request->input('name')}' created successfully.");
        } catch (Exception $e) {
            return redirect()->route('pulse-devops.secrets')
                ->with('status', 'error')
                ->with('message', 'Failed to create secret: '.$e->getMessage());
        }
    }

    public function update(Request $request, string $name): RedirectResponse
    {
        $request->validate([
            'value' => 'required|string',
        ]);

        try {
            $this->secretManager->updateSecret($name, $request->input('value'));

            return redirect()->route('pulse-devops.secrets')
                ->with('status', 'success')
                ->with('message', "Secret '{$name}' updated with a new version.");
        } catch (Exception $e) {
            return redirect()->route('pulse-devops.secrets')
                ->with('status', 'error')
                ->with('message', 'Failed to update secret: '.$e->getMessage());
        }
    }

    public function value(string $name): JsonResponse
    {
        try {
            $value = $this->secretManager->getSecretValue($name);

            return response()->json(['value' => $value]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
