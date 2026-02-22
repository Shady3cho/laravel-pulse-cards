<?php

namespace LangtonMwanza\PulseDevops\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LangtonMwanza\PulseDevops\Services\MigrationService;

class MigrationsController extends Controller
{
    public function __construct(
        protected MigrationService $migrationService,
    ) {}

    public function index()
    {
        if (! config('pulse-devops.cards.migrations', true)) {
            abort(404);
        }

        $ran = $this->migrationService->getRanMigrations();
        $pending = $this->migrationService->getPendingMigrations();
        $lastBatch = $this->migrationService->getLastBatch();
        $lastBatchMigrations = $lastBatch > 0
            ? $this->migrationService->getMigrationsInBatch($lastBatch)
            : [];

        return view('pulse-devops::pages.migrations', compact(
            'ran',
            'pending',
            'lastBatch',
            'lastBatchMigrations',
        ));
    }

    public function run(Request $request): RedirectResponse
    {
        $output = $this->migrationService->runMigrations();

        return redirect()->route('pulse-devops.migrations')
            ->with('output', $output)
            ->with('status', 'success')
            ->with('message', 'Migrations executed successfully.');
    }

    public function rollback(Request $request): RedirectResponse
    {
        $output = $this->migrationService->rollbackLastBatch();

        return redirect()->route('pulse-devops.migrations')
            ->with('output', $output)
            ->with('status', 'success')
            ->with('message', 'Rollback completed successfully.');
    }
}
