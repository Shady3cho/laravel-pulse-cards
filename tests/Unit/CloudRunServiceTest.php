<?php

use LangtonMwanza\PulseDevops\Services\CloudRunService;

it('reports not configured when project id is missing', function () {
    config()->set('pulse-devops.gcp.project_id', null);

    $service = new CloudRunService();
    expect($service->isConfigured())->toBeFalse();
});

it('reports configured when project id is set', function () {
    config()->set('pulse-devops.gcp.project_id', 'my-project');

    $service = new CloudRunService();
    expect($service->isConfigured())->toBeTrue();
});

it('uses configured region', function () {
    config()->set('pulse-devops.gcp.region', 'europe-west1');

    $service = new CloudRunService();
    $reflection = new ReflectionClass($service);
    $property = $reflection->getProperty('region');
    $property->setAccessible(true);

    expect($property->getValue($service))->toBe('europe-west1');
});
