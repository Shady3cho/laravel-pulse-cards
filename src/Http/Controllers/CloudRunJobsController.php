<?php

namespace LangtonMwanza\PulseDevops\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use LangtonMwanza\PulseDevops\Services\CloudRunService;

class CloudRunJobsController extends Controller
{
    public function __construct(
        protected CloudRunService $cloudRunService,
    ) {}

    public function index()
    {
        if (! config('pulse-devops.cards.cloud_run_jobs', true)) {
            abort(404);
        }

        $configured = $this->cloudRunService->isConfigured();
        $jobs = [];
        $error = null;

        if ($configured) {
            try {
                $jobs = $this->cloudRunService->listJobs();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        return view('pulse-devops::pages.cloud-run-jobs', compact('configured', 'jobs', 'error'));
    }

    public function execute(string $name): RedirectResponse
    {
        try {
            $this->cloudRunService->executeJob($name);

            return redirect()->route('pulse-devops.cloud-run-jobs')
                ->with('status', 'success')
                ->with('message', "Job '{$name}' execution triggered.");
        } catch (Exception $e) {
            return redirect()->route('pulse-devops.cloud-run-jobs')
                ->with('status', 'error')
                ->with('message', 'Failed to execute job: '.$e->getMessage());
        }
    }
}
