<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use LangtonMwanza\PulseDevops\Services\MigrationService;

beforeEach(function () {
    Schema::create('migrations', function ($table) {
        $table->increments('id');
        $table->string('migration');
        $table->integer('batch');
    });
});

it('returns ran migrations from the database', function () {
    DB::table('migrations')->insert([
        ['migration' => '2024_01_01_000001_create_users_table', 'batch' => 1],
        ['migration' => '2024_01_01_000002_create_posts_table', 'batch' => 1],
    ]);

    $service = new MigrationService();
    $ran = $service->getRanMigrations();

    expect($ran)->toHaveCount(2);
    expect($ran[0]['migration'])->toBe('2024_01_01_000001_create_users_table');
    expect($ran[0]['batch'])->toBe(1);
});

it('returns the last batch number', function () {
    DB::table('migrations')->insert([
        ['migration' => '2024_01_01_000001_create_users_table', 'batch' => 1],
        ['migration' => '2024_01_01_000002_create_posts_table', 'batch' => 2],
    ]);

    $service = new MigrationService();
    expect($service->getLastBatch())->toBe(2);
});

it('returns migrations in a specific batch', function () {
    DB::table('migrations')->insert([
        ['migration' => '2024_01_01_000001_create_users_table', 'batch' => 1],
        ['migration' => '2024_01_01_000002_create_posts_table', 'batch' => 2],
        ['migration' => '2024_01_01_000003_create_comments_table', 'batch' => 2],
    ]);

    $service = new MigrationService();
    $batch2 = $service->getMigrationsInBatch(2);

    expect($batch2)->toHaveCount(2);
    expect($batch2)->toContain('2024_01_01_000002_create_posts_table');
    expect($batch2)->toContain('2024_01_01_000003_create_comments_table');
});

it('returns correct status summary', function () {
    DB::table('migrations')->insert([
        ['migration' => '2024_01_01_000001_create_users_table', 'batch' => 1],
    ]);

    File::shouldReceive('isDirectory')->andReturn(true);
    File::shouldReceive('files')->andReturn(collect([
        new SplFileInfo(database_path('migrations/2024_01_01_000001_create_users_table.php')),
        new SplFileInfo(database_path('migrations/2024_01_01_000002_create_posts_table.php')),
    ]));

    $service = new MigrationService();
    $status = $service->getStatus();

    expect($status['total'])->toBe(2);
    expect($status['ran'])->toBe(1);
    expect($status['pending'])->toBe(1);
    expect($status['is_up_to_date'])->toBeFalse();
});

it('reports up to date when no pending migrations', function () {
    DB::table('migrations')->insert([
        ['migration' => '2024_01_01_000001_create_users_table', 'batch' => 1],
    ]);

    File::shouldReceive('isDirectory')->andReturn(true);
    File::shouldReceive('files')->andReturn(collect([
        new SplFileInfo(database_path('migrations/2024_01_01_000001_create_users_table.php')),
    ]));

    $service = new MigrationService();
    $status = $service->getStatus();

    expect($status['is_up_to_date'])->toBeTrue();
    expect($status['pending'])->toBe(0);
});
