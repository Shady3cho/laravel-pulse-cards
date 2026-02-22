<div wire:poll.15s="loadData">
    @if($enabled)
    <x-pulse::card :cols="$cols ?? 2" :rows="$rows ?? 1" :class="$class ?? ''">
        <x-pulse::card-header name="Queue Health">
        </x-pulse::card-header>

        <div class="px-4 py-3 space-y-2">
            @if($error)
                <p class="text-xs text-red-400">{{ $error }}</p>
            @else
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-400">Driver</span>
                    <span class="text-sm font-semibold text-gray-200">{{ $driver }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-400">Pending Jobs</span>
                    <span class="text-sm font-semibold text-gray-200">{{ number_format($pendingJobs) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-400">Failed Jobs</span>
                    @if($failedJobs === 0)
                        <span class="inline-flex items-center rounded-full bg-green-500/10 px-2 py-0.5 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-500/20">
                            {{ $failedJobs }}
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-red-500/10 px-2 py-0.5 text-xs font-medium text-red-400 ring-1 ring-inset ring-red-500/20">
                            {{ number_format($failedJobs) }}
                        </span>
                    @endif
                </div>

                @if($actionMessage)
                    <p class="text-xs text-blue-400 mt-1">{{ $actionMessage }}</p>
                @endif

                <div class="flex gap-2 mt-2 pt-2 border-t border-gray-700">
                    @if($failedJobs > 0)
                        <button wire:click="retryAll"
                                wire:confirm="Retry all failed jobs?"
                                class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1 rounded transition-colors">
                            Retry All
                        </button>
                        <button wire:click="flushFailed"
                                wire:confirm="Delete all failed jobs? This cannot be undone."
                                class="text-xs bg-red-600 hover:bg-red-700 text-white px-2.5 py-1 rounded transition-colors">
                            Flush Failed
                        </button>
                    @else
                        <span class="text-xs text-gray-500">No failed jobs</span>
                    @endif
                </div>
            @endif
        </div>
    </x-pulse::card>
    @endif
</div>
