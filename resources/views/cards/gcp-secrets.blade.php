<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header name="GCP Secrets">
        <x-slot:actions>
            @if($configured)
                <a href="{{ route('pulse-devops.secrets') }}"
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
                <span class="text-sm text-gray-400">Secrets</span>
                <span class="text-sm font-semibold text-gray-200">{{ $secretCount }}</span>
            </div>
        @endif
    </div>
</x-pulse::card>
