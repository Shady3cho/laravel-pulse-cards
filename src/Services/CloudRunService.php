<?php

namespace LangtonMwanza\PulseDevops\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class CloudRunService
{
    protected ?string $projectId;

    protected string $region;

    public function __construct()
    {
        $this->projectId = config('pulse-devops.gcp.project_id');
        $this->region = config('pulse-devops.gcp.region', 'us-central1');
    }

    public function isConfigured(): bool
    {
        return ! empty($this->projectId);
    }

    public function listJobs(): array
    {
        $url = $this->baseUrl().'/jobs';

        $response = Http::withToken($this->getAccessToken())
            ->get($url);

        if (! $response->successful()) {
            throw new Exception('Failed to list Cloud Run jobs: '.$response->body());
        }

        $data = $response->json();

        return array_map(function ($job) {
            $name = $this->extractResourceName($job['name'] ?? '');

            return [
                'name' => $name,
                'full_name' => $job['name'] ?? '',
                'creation_time' => $job['createTime'] ?? null,
                'update_time' => $job['updateTime'] ?? null,
                'launch_stage' => $job['launchStage'] ?? 'GA',
                'last_execution' => $this->parseExecution($job['latestCreatedExecution'] ?? null),
                'execution_count' => $job['executionCount'] ?? 0,
            ];
        }, $data['jobs'] ?? []);
    }

    public function getJobExecutions(string $jobName): array
    {
        $url = $this->baseUrl().'/jobs/'.$jobName.'/executions';

        $response = Http::withToken($this->getAccessToken())
            ->get($url);

        if (! $response->successful()) {
            throw new Exception('Failed to list executions: '.$response->body());
        }

        $data = $response->json();

        return array_map(function ($execution) {
            return [
                'name' => $this->extractResourceName($execution['name'] ?? ''),
                'full_name' => $execution['name'] ?? '',
                'start_time' => $execution['startTime'] ?? $execution['createTime'] ?? null,
                'completion_time' => $execution['completionTime'] ?? null,
                'succeeded_count' => $execution['succeededCount'] ?? 0,
                'failed_count' => $execution['failedCount'] ?? 0,
                'running_count' => $execution['runningCount'] ?? 0,
                'status' => $this->determineExecutionStatus($execution),
            ];
        }, $data['executions'] ?? []);
    }

    public function executeJob(string $jobName): array
    {
        $url = $this->baseUrl().'/jobs/'.$jobName.':run';

        $response = Http::withToken($this->getAccessToken())
            ->post($url);

        if (! $response->successful()) {
            throw new Exception('Failed to execute job: '.$response->body());
        }

        return $response->json();
    }

    protected function baseUrl(): string
    {
        return sprintf(
            'https://run.googleapis.com/v2/projects/%s/locations/%s',
            $this->projectId,
            $this->region,
        );
    }

    protected function getAccessToken(): string
    {
        // On Cloud Run: fetch from metadata server
        $metadataUrl = 'http://metadata.google.internal/computeMetadata/v1/instance/service-accounts/default/access_token';

        try {
            $context = stream_context_create([
                'http' => [
                    'header' => 'Metadata-Flavor: Google',
                    'timeout' => 3,
                ],
            ]);

            $response = @file_get_contents($metadataUrl, false, $context);

            if ($response !== false) {
                $data = json_decode($response, true);

                return $data['access_token'] ?? '';
            }
        } catch (Exception) {
            // Not on Cloud Run, try local gcloud CLI
        }

        // Local development: use gcloud CLI
        $token = trim((string) shell_exec('gcloud auth print-access-token 2>/dev/null'));

        if (empty($token)) {
            throw new Exception(
                'Unable to obtain GCP access token. On Cloud Run, ensure the service account has proper permissions. '
                .'Locally, run: gcloud auth application-default login'
            );
        }

        return $token;
    }

    protected function extractResourceName(string $fullName): string
    {
        $parts = explode('/', $fullName);

        return end($parts);
    }

    protected function parseExecution(?array $execution): ?array
    {
        if (! $execution) {
            return null;
        }

        return [
            'name' => $this->extractResourceName($execution['name'] ?? ''),
            'create_time' => $execution['createTime'] ?? null,
            'completion_time' => $execution['completionTime'] ?? null,
        ];
    }

    protected function determineExecutionStatus(array $execution): string
    {
        if (($execution['failedCount'] ?? 0) > 0) {
            return 'failed';
        }

        if (($execution['runningCount'] ?? 0) > 0) {
            return 'running';
        }

        if (($execution['succeededCount'] ?? 0) > 0) {
            return 'succeeded';
        }

        if (! empty($execution['completionTime'])) {
            return 'completed';
        }

        return 'pending';
    }
}
