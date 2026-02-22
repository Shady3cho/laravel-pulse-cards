<div wire:poll.60s="loadData">
    @if($enabled)
    <x-pulse::card :cols="$cols ?? 2" :rows="$rows ?? 1" :class="$class ?? ''">
        <x-pulse::card-header name="Database">
            <x-slot:actions>
                <a href="{{ route('pulse-devops.database') }}"
                   class="text-xs text-gray-400 hover:text-gray-200 transition-colors">
                    Details &rarr;
                </a>
            </x-slot:actions>
        </x-pulse::card-header>

        <div class="px-4 py-3 space-y-2">
            @if($error)
                <p class="text-xs text-red-400">{{ $error }}</p>
            @elseif(!empty($size))
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-400">Tables</span>
                    <span class="text-sm font-semibold text-gray-200">{{ $size['table_count'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-400">Total Size</span>
                    <span class="text-sm font-semibold text-gray-200">{{ $size['total_size_formatted'] }}</span>
                </div>

                @if(count($topTables) > 0)
                    <div class="mt-3 border-t border-gray-700 pt-2">
                        <p class="text-xs text-gray-500 mb-1">Largest Tables</p>
                        @foreach($topTables as $table)
                            <div class="flex items-center justify-between py-0.5">
                                <span class="text-xs text-gray-400 truncate mr-2">{{ $table['name'] }}</span>
                                <span class="text-xs text-gray-300 whitespace-nowrap">{{ $table['total_size_formatted'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <p class="text-xs text-gray-500">Loading...</p>
            @endif
        </div>
    </x-pulse::card>
    @endif
</div>
