<?php

namespace LangtonMwanza\PulseDevops\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MigrationService
{
    public function getRanMigrations(): array
    {
        return DB::table('migrations')
            ->orderBy('batch')
            ->orderBy('migration')
            ->get()
            ->map(fn ($row) => [
                'migration' => $row->migration,
                'batch' => $row->batch,
            ])
            ->all();
    }

    public function getPendingMigrations(): array
    {
        $ran = collect($this->getRanMigrations())->pluck('migration')->all();
        $files = $this->getMigrationFiles();

        return array_values(array_diff($files, $ran));
    }

    public function getMigrationFiles(): array
    {
        $path = database_path('migrations');

        if (! File::isDirectory($path)) {
            return [];
        }

        return collect(File::files($path))
            ->map(fn ($file) => str_replace('.php', '', $file->getFilename()))
            ->sort()
            ->values()
            ->all();
    }

    public function getStatus(): array
    {
        $ran = $this->getRanMigrations();
        $pending = $this->getPendingMigrations();

        return [
            'total' => count($ran) + count($pending),
            'ran' => count($ran),
            'pending' => count($pending),
            'is_up_to_date' => count($pending) === 0,
        ];
    }

    public function getLastBatch(): int
    {
        return (int) DB::table('migrations')->max('batch');
    }

    public function getMigrationsInBatch(int $batch): array
    {
        return DB::table('migrations')
            ->where('batch', $batch)
            ->orderBy('migration')
            ->pluck('migration')
            ->all();
    }

    public function runMigrations(): string
    {
        Artisan::call('migrate', ['--force' => true]);

        return Artisan::output();
    }

    public function rollbackLastBatch(): string
    {
        Artisan::call('migrate:rollback', ['--force' => true]);

        return Artisan::output();
    }
}
