<?php

namespace LangtonMwanza\PulseDevops\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DatabaseInspector
{
    public function getTables(): Collection
    {
        $tables = DB::select('SHOW TABLE STATUS');

        return collect($tables)->map(function ($table) {
            $dataSize = $table->Data_length ?? 0;
            $indexSize = $table->Index_length ?? 0;
            $totalSize = $dataSize + $indexSize;

            return [
                'name' => $table->Name,
                'rows' => $table->Rows ?? 0,
                'data_size' => $dataSize,
                'index_size' => $indexSize,
                'total_size' => $totalSize,
                'data_size_formatted' => $this->formatBytes($dataSize),
                'index_size_formatted' => $this->formatBytes($indexSize),
                'total_size_formatted' => $this->formatBytes($totalSize),
                'engine' => $table->Engine ?? 'Unknown',
                'collation' => $table->Collation ?? 'Unknown',
            ];
        })->sortByDesc('total_size')->values();
    }

    public function getDatabaseSize(): array
    {
        $tables = $this->getTables();

        $totalData = $tables->sum('data_size');
        $totalIndex = $tables->sum('index_size');
        $totalCombined = $totalData + $totalIndex;

        return [
            'table_count' => $tables->count(),
            'data_size' => $totalData,
            'index_size' => $totalIndex,
            'total_size' => $totalCombined,
            'data_size_formatted' => $this->formatBytes($totalData),
            'index_size_formatted' => $this->formatBytes($totalIndex),
            'total_size_formatted' => $this->formatBytes($totalCombined),
        ];
    }

    public function getTopTables(int $limit = 3): Collection
    {
        return $this->getTables()->take($limit);
    }

    public function formatBytes(int $bytes, int $precision = 2): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(log($bytes, 1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes / (1024 ** $pow), $precision).' '.$units[$pow];
    }
}
