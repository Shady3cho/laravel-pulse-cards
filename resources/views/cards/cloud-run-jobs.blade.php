<div>
    @if($enabled)
    <x-pulse::card :cols="$cols ?? 2" :rows="$rows ?? 1" :class="$class ?? ''">
        <x-pulse::card-header name="Cloud Run Jobs">
            <x-slot:actions>
                @if($configured)
                    <a href="{{ route('pulse-devops.cloud-run-jobs') }}"
                       class="text-xs text-gray-400 hover:text-gray-200 transition-colors">
                        Manage &rarr;
                    </a>
                @endif
            </x-slot:actions>
        </x-pulse::card-header>

        <div class="px-4 py-3 space-y-2">
            @if(!$configured)
                <p class="text-xs text-gray-500">
                    GCP not configured. Set <code class="text-xs bg-gray-800 px-1 rounded">GOOGLE_CLOUD_PROJECT_ID</code> in your environment.
                </p>
            @elseif($error)
                <p class="text-xs text-red-400">{{ $error }}</p>
            @else
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-400">Jobs</span>
                    <span class="text-sm font-semibold text-gray-200">{{ $jobCount }}</span>
                </div>

                @if($lastExecution)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-400">Last Run</span>
                        <span class="text-xs text-gray-300">{{ $lastExecution['create_time'] ?? 'N/A' }}</span>
                    </div>
                @endif
            @endif
        </div>
    </x-pulse::card>
    @endif
</div>
