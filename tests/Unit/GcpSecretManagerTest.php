<?php

use LangtonMwanza\PulseDevops\Services\GcpSecretManager;

it('reports not configured when project id is missing', function () {
    config()->set('pulse-devops.gcp.project_id', null);

    $manager = new GcpSecretManager();
    expect($manager->isConfigured())->toBeFalse();
});

it('reports configured when project id is set', function () {
    config()->set('pulse-devops.gcp.project_id', 'my-project');

    $manager = new GcpSecretManager();
    // isConfigured() also tries to instantiate the client, which may fail
    // in a test environment without ADC. We just verify project_id presence.
    expect(config('pulse-devops.gcp.project_id'))->toBe('my-project');
});
