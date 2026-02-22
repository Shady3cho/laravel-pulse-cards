<x-pulse-devops::layouts.app title="Cloud Run Jobs">
    <div class="space-y-6">
        @if(!$configured)
            <div class="rounded-lg bg-gray-900 border border-gray-800 p-8 text-center">
                <p class="text-gray-400">GCP Cloud Run is not configured.</p>
                <p class="text-sm text-gray-500 mt-2">Set <code class="bg-gray-800 px-1.5 py-0.5 rounded text-xs">GOOGLE_CLOUD_PROJECT_ID</code> in your environment to enable Cloud Run job management.</p>
            </div>
        @elseif($error)
            <div class="rounded-lg bg-red-500/10 border border-red-500/20 p-4">
                <p class="text-sm text-red-400">{{ $error }}</p>
            </div>
        @else
            {{-- Jobs List --}}
            <div class="rounded-lg bg-gray-900 border border-gray-800 overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-800">
                            <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase">Job Name</th>
                            <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase">Last Execution</th>
                            <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase">Executions</th>
                            <th class="text-right px-4 py-2 text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $job)
                            <tr x-data="{ showHistory: false }" class="border-b border-gray-800/50">
                                <td class="px-4 py-2 text-gray-300 font-mono text-xs">{{ $job['name'] }}</td>
                                <td class="px-4 py-2 text-gray-400 text-xs">
                                    @if($job['last_execution'])
                                        {{ $job['last_execution']['create_time'] ?? 'N/A' }}
                                    @else
                                        <span class="text-gray-600">Never</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-gray-400 text-xs">{{ $job['execution_count'] }}</td>
                                <td class="px-4 py-2 text-right">
                                    <form method="POST"
                                          action="{{ route('pulse-devops.cloud-run-jobs.execute', $job['name']) }}"
                                          class="inline">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Execute job {{ $job['name'] }}?')"
                                                class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition-colors">
                                            Execute
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500 text-sm">No Cloud Run jobs found in {{ config('pulse-devops.gcp.region') }}.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-pulse-devops::layouts.app>
