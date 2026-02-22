<x-pulse-devops::layouts.app title="Database Tables">
    <div x-data="{ search: '' }" class="space-y-6">
        {{-- Size Summary --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="rounded-lg bg-gray-900 border border-gray-800 p-4">
                <span class="text-xs text-gray-500 block">Tables</span>
                <span class="text-2xl font-bold text-gray-200">{{ $size['table_count'] }}</span>
            </div>
            <div class="rounded-lg bg-gray-900 border border-gray-800 p-4">
                <span class="text-xs text-gray-500 block">Data Size</span>
                <span class="text-2xl font-bold text-blue-400">{{ $size['data_size_formatted'] }}</span>
            </div>
            <div class="rounded-lg bg-gray-900 border border-gray-800 p-4">
                <span class="text-xs text-gray-500 block">Total Size (Data + Index)</span>
                <span class="text-2xl font-bold text-gray-200">{{ $size['total_size_formatted'] }}</span>
            </div>
        </div>

        {{-- Search --}}
        <div>
            <input type="text"
                   x-model="search"
                   placeholder="Filter tables..."
                   class="w-full sm:w-64 rounded-lg bg-gray-900 border border-gray-700 px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        {{-- Table List --}}
        <div class="rounded-lg bg-gray-900 border border-gray-800 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase">Table Name</th>
                        <th class="text-right px-4 py-2 text-xs font-medium text-gray-500 uppercase">Rows</th>
                        <th class="text-right px-4 py-2 text-xs font-medium text-gray-500 uppercase">Data Size</th>
                        <th class="text-right px-4 py-2 text-xs font-medium text-gray-500 uppercase">Index Size</th>
                        <th class="text-right px-4 py-2 text-xs font-medium text-gray-500 uppercase">Total Size</th>
                        <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase">Engine</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tables as $table)
                        <tr class="border-b border-gray-800/50"
                            x-show="!search || '{{ strtolower($table['name']) }}'.includes(search.toLowerCase())">
                            <td class="px-4 py-2 text-gray-300 font-mono text-xs">{{ $table['name'] }}</td>
                            <td class="px-4 py-2 text-gray-400 text-right">{{ number_format($table['rows']) }}</td>
                            <td class="px-4 py-2 text-gray-400 text-right">{{ $table['data_size_formatted'] }}</td>
                            <td class="px-4 py-2 text-gray-400 text-right">{{ $table['index_size_formatted'] }}</td>
                            <td class="px-4 py-2 text-gray-200 text-right font-medium">{{ $table['total_size_formatted'] }}</td>
                            <td class="px-4 py-2 text-gray-500 text-xs">{{ $table['engine'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-pulse-devops::layouts.app>
