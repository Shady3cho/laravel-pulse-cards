<?php

use LangtonMwanza\PulseDevops\Services\DatabaseInspector;

it('formats bytes correctly', function () {
    $inspector = new DatabaseInspector();

    expect($inspector->formatBytes(0))->toBe('0 B');
    expect($inspector->formatBytes(1024))->toBe('1 KB');
    expect($inspector->formatBytes(1048576))->toBe('1 MB');
    expect($inspector->formatBytes(1073741824))->toBe('1 GB');
    expect($inspector->formatBytes(500))->toBe('500 B');
    expect($inspector->formatBytes(1536))->toBe('1.5 KB');
});

it('formats bytes with custom precision', function () {
    $inspector = new DatabaseInspector();

    expect($inspector->formatBytes(1536, 0))->toBe('2 KB');
    expect($inspector->formatBytes(1536, 1))->toBe('1.5 KB');
    expect($inspector->formatBytes(1536, 3))->toBe('1.5 KB');
});
