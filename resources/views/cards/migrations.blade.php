<div wire:poll.30s="loadData">
    @if($enabled)
    <x-pulse::card :cols="$cols ?? 2" :rows="$rows ?? 1" :class="$class ?? ''">
        <x-pulse::card-header name="Migrations">
            <x-slot:actions>
                <a href="{{ route('pulse-devops.migrations') }}"
                   class="text-xs text-gray-400 hover:text-gray-200 transition-colors">
                    Manage &rarr;
                </a>
            </x-slot:actions>
        </x-pulse::card-header>

        <div class="px-4 py-3 space-y-2">
            @if($error)
                <p class="text-xs text-red-400">{{ $error }}</p>
            @elseif(!empty($status))
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-400">Total</span>
                    <span class="text-sm font-semibold text-gray-200">{{ $status['total'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-400">Ran</span>
                    <span class="text-sm font-semibold text-gray-200">{{ $status['ran'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-400">Pending</span>
                    @if($status['is_up_to_date'])
                        <span class="inline-flex items-center rounded-full bg-green-500/10 px-2 py-0.5 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-500/20">
                            Up to date
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-orange-500/10 px-2 py-0.5 text-xs font-medium text-orange-400 ring-1 ring-inset ring-orange-500/20">
                            {{ $status['pending'] }} pending
                        </span>
                    @endif
                </div>
            @else
                <p class="text-xs text-gray-500">Loading...</p>
            @endif
        </div>
    </x-pulse::card>
    @endif
</div>
