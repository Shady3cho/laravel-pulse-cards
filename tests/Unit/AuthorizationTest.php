<?php

use Illuminate\Support\Facades\Gate;

it('allows access in local environment', function () {
    app()->detectEnvironment(fn () => 'local');

    expect(Gate::allows('viewPulseDevops'))->toBeTrue();
});

it('denies access in production without user', function () {
    app()->detectEnvironment(fn () => 'production');
    config()->set('pulse-devops.allowed_emails', []);

    expect(Gate::allows('viewPulseDevops'))->toBeFalse();
});
