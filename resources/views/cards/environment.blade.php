<div>
    @if($enabled)
    <x-pulse::card :cols="$cols ?? 6" :rows="$rows ?? 1" :class="$class ?? ''">
        <x-pulse::card-header name="Environment">
        </x-pulse::card-header>

        <div class="px-4 py-3">
            @if($error)
                <p class="text-xs text-red-400">{{ $error }}</p>
            @elseif(!empty($info))
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-2">
                    <div>
                        <span class="text-xs text-gray-500 block">App Name</span>
                        <span class="text-sm text-gray-200">{{ $info['app_name'] }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Environment</span>
                        <span class="text-sm text-gray-200">{{ $info['environment'] }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Debug Mode</span>
                        @if($info['debug'])
                            <span class="inline-flex items-center rounded-full bg-red-500/10 px-2 py-0.5 text-xs font-medium text-red-400 ring-1 ring-inset ring-red-500/20">ON</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-green-500/10 px-2 py-0.5 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-500/20">OFF</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">PHP Version</span>
                        <span class="text-sm text-gray-200">{{ $info['php_version'] }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Laravel Version</span>
                        <span class="text-sm text-gray-200">{{ $info['laravel_version'] }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Database</span>
                        <span class="text-sm text-gray-200">
                            {{ $info['db_driver'] }} / {{ $info['db_name'] }}
                            @if($info['db_connected'])
                                <span class="inline-block w-1.5 h-1.5 bg-green-400 rounded-full ml-1"></span>
                            @else
                                <span class="inline-block w-1.5 h-1.5 bg-red-400 rounded-full ml-1"></span>
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Cache Driver</span>
                        <span class="text-sm text-gray-200">{{ $info['cache_driver'] }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Session Driver</span>
                        <span class="text-sm text-gray-200">{{ $info['session_driver'] }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Filesystem</span>
                        <span class="text-sm text-gray-200">{{ $info['filesystem_disk'] }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Mail Driver</span>
                        <span class="text-sm text-gray-200">{{ $info['mail_driver'] }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Mail From</span>
                        <span class="text-sm text-gray-200 truncate block">{{ $info['mail_from'] ?? 'Not set' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Queue Driver</span>
                        <span class="text-sm text-gray-200">{{ $info['queue_driver'] }}</span>
                    </div>
                </div>
            @else
                <p class="text-xs text-gray-500">Loading...</p>
            @endif
        </div>
    </x-pulse::card>
    @endif
</div>
