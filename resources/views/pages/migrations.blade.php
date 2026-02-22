<x-pulse-devops::layouts.app title="Migrations">
    <div class="space-y-6">
        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('pulse-devops.migrations.run') }}">
                @csrf
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors"
                        onclick="return confirm('Run all pending migrations with --force?')">
                    Run Migrations
                </button>
            </form>

            @if($lastBatch > 0)
                <form method="POST" action="{{ route('pulse-devops.migrations.rollback') }}">
                    @csrf
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors"
                            onclick="return confirm('Rollback the last batch (batch {{ $lastBatch }})? This will revert: {{ implode(', ', $lastBatchMigrations) }}')">
                        Rollback Batch {{ $lastBatch }}
                    </button>
                </form>
            @endif
        </div>

        {{-- Command Output --}}
        @if(session('output'))
            <div class="rounded-lg bg-gray-900 border border-gray-800 p-4">
                <h3 class="text-sm font-medium text-gray-300 mb-2">Command Output</h3>
                <pre class="text-xs text-gray-400 font-mono whitespace-pre-wrap">{{ session('output') }}</pre>
            </div>
        @endif

        {{-- Status Summary --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="rounded-lg bg-gray-900 border border-gray-800 p-4">
                <span class="text-xs text-gray-500 block">Total Migrations</span>
                <span class="text-2xl font-bold text-gray-200">{{ count($ran) + count($pending) }}</span>
            </div>
            <div class="rounded-lg bg-gray-900 border border-gray-800 p-4">
                <span class="text-xs text-gray-500 block">Ran</span>
                <span class="text-2xl font-bold text-green-400">{{ count($ran) }}</span>
            </div>
            <div class="rounded-lg bg-gray-900 border border-gray-800 p-4">
                <span class="text-xs text-gray-500 block">Pending</span>
                <span class="text-2xl font-bold {{ count($pending) > 0 ? 'text-orange-400' : 'text-green-400' }}">{{ count($pending) }}</span>
            </div>
        </div>

        {{-- Pending Migrations --}}
        @if(count($pending) > 0)
            <div class="rounded-lg bg-gray-900 border border-gray-800 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-800">
                    <h3 class="text-sm font-medium text-orange-400">Pending Migrations</h3>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-800">
                            <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase">Migration</th>
                            <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pending as $migration)
                            <tr class="border-b border-gray-800/50">
                                <td class="px-4 py-2 text-gray-300 font-mono text-xs">{{ $migration }}</td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center rounded-full bg-orange-500/10 px-2 py-0.5 text-xs font-medium text-orange-400 ring-1 ring-inset ring-orange-500/20">
                                        Pending
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Ran Migrations --}}
        <div class="rounded-lg bg-gray-900 border border-gray-800 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-800">
                <h3 class="text-sm font-medium text-gray-300">Ran Migrations</h3>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase">Migration</th>
                        <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase">Batch</th>
                        <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ran as $migration)
                        <tr class="border-b border-gray-800/50">
                            <td class="px-4 py-2 text-gray-300 font-mono text-xs">{{ $migration['migration'] }}</td>
                            <td class="px-4 py-2 text-gray-400">{{ $migration['batch'] }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center rounded-full bg-green-500/10 px-2 py-0.5 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-500/20">
                                    Ran
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500 text-sm">No migrations have been run.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Last Batch Info --}}
        @if($lastBatch > 0 && count($lastBatchMigrations) > 0)
            <div class="rounded-lg bg-gray-900 border border-gray-800 p-4">
                <h3 class="text-sm font-medium text-gray-300 mb-2">Last Batch (#{{ $lastBatch }})</h3>
                <ul class="space-y-1">
                    @foreach($lastBatchMigrations as $m)
                        <li class="text-xs text-gray-400 font-mono">{{ $m }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-pulse-devops::layouts.app>
