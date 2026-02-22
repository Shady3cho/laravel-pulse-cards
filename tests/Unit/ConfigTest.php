<?php

it('has default configuration values', function () {
    expect(config('pulse-devops.authorize'))->toBeTrue();
    expect(config('pulse-devops.gcp.project_id'))->toBe('test-project');
    expect(config('pulse-devops.gcp.region'))->toBe('us-central1');
});

it('has all card feature flags enabled by default', function () {
    expect(config('pulse-devops.cards.migrations'))->toBeTrue();
    expect(config('pulse-devops.cards.database_tables'))->toBeTrue();
    expect(config('pulse-devops.cards.gcp_secrets'))->toBeTrue();
    expect(config('pulse-devops.cards.cloud_run_jobs'))->toBeTrue();
    expect(config('pulse-devops.cards.queue_health'))->toBeTrue();
    expect(config('pulse-devops.cards.environment'))->toBeTrue();
});

it('allows disabling individual cards', function () {
    config()->set('pulse-devops.cards.migrations', false);
    expect(config('pulse-devops.cards.migrations'))->toBeFalse();
    expect(config('pulse-devops.cards.database_tables'))->toBeTrue();
});
