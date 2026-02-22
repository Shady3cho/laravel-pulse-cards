<x-pulse-devops::layouts.app title="GCP Secrets">
    <div x-data="{
        showCreate: false,
        showSecret: {},
        secretValues: {},
        loading: {},
        async fetchValue(name) {
            this.loading[name] = true;
            try {
                const resp = await fetch('{{ url('pulse/devops/secrets') }}/' + name + '/value');
                const data = await resp.json();
                this.secretValues[name] = data.value || data.error || 'Unable to fetch';
            } catch (e) {
                this.secretValues[name] = 'Error fetching value';
            }
            this.loading[name] = false;
        },
        toggleSecret(name) {
            if (!this.showSecret[name]) {
                if (!this.secretValues[name]) {
                    this.fetchValue(name);
                }
                this.showSecret[name] = true;
            } else {
                this.showSecret[name] = false;
            }
        }
    }" class="space-y-6">
        @if(!$configured)
            <div class="rounded-lg bg-gray-900 border border-gray-800 p-8 text-center">
                <p class="text-gray-400">GCP Secret Manager is not configured.</p>
                <p class="text-sm text-gray-500 mt-2">Set <code class="bg-gray-800 px-1.5 py-0.5 rounded text-xs">GOOGLE_CLOUD_PROJECT_ID</code> in your environment to enable secrets management.</p>
            </div>
        @elseif($error)
            <div class="rounded-lg bg-red-500/10 border border-red-500/20 p-4">
                <p class="text-sm text-red-400">{{ $error }}</p>
            </div>
        @else
            {{-- Actions --}}
            <div class="flex items-center gap-3">
                <button @click="showCreate = !showCreate"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    Create Secret
                </button>
            </div>

            {{-- Create Form --}}
            <div x-show="showCreate" x-cloak class="rounded-lg bg-gray-900 border border-gray-800 p-4">
                <form method="POST" action="{{ route('pulse-devops.secrets.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Secret Name</label>
                        <input type="text" name="name" required
                               pattern="[a-zA-Z0-9_-]+"
                               placeholder="MY_SECRET_NAME"
                               class="w-full sm:w-80 rounded-lg bg-gray-800 border border-gray-700 px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Value</label>
                        <textarea name="value" required rows="3"
                                  placeholder="Secret value..."
                                  class="w-full rounded-lg bg-gray-800 border border-gray-700 px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                            Create
                        </button>
                        <button type="button" @click="showCreate = false"
                                class="bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            {{-- Secrets List --}}
            <div class="rounded-lg bg-gray-900 border border-gray-800 overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-800">
                            <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase">Created</th>
                            <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase">Value</th>
                            <th class="text-right px-4 py-2 text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($secrets as $secret)
                            <tr class="border-b border-gray-800/50">
                                <td class="px-4 py-2 text-gray-300 font-mono text-xs">{{ $secret['name'] }}</td>
                                <td class="px-4 py-2 text-gray-400 text-xs">{{ $secret['created'] ?? 'N/A' }}</td>
                                <td class="px-4 py-2">
                                    <div class="flex items-center gap-2">
                                        <template x-if="showSecret['{{ $secret['name'] }}']">
                                            <code class="text-xs text-gray-300 bg-gray-800 px-2 py-1 rounded font-mono break-all"
                                                  x-text="secretValues['{{ $secret['name'] }}'] || 'Loading...'"></code>
                                        </template>
                                        <template x-if="!showSecret['{{ $secret['name'] }}']">
                                            <span class="text-gray-600 text-xs">••••••••</span>
                                        </template>
                                        <button @click="toggleSecret('{{ $secret['name'] }}')"
                                                class="text-xs text-blue-400 hover:text-blue-300 transition-colors">
                                            <span x-text="showSecret['{{ $secret['name'] }}'] ? 'Hide' : 'Show'">Show</span>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <div x-data="{ showUpdate: false }" class="inline-block">
                                        <button @click="showUpdate = !showUpdate"
                                                class="text-xs text-orange-400 hover:text-orange-300 transition-colors">
                                            Update
                                        </button>
                                        <div x-show="showUpdate" x-cloak
                                             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                                             @click.self="showUpdate = false">
                                            <div class="bg-gray-900 border border-gray-700 rounded-lg p-6 w-full max-w-md mx-4" @click.stop>
                                                <h3 class="text-sm font-medium text-gray-200 mb-4">Update Secret: {{ $secret['name'] }}</h3>
                                                <form method="POST" action="{{ route('pulse-devops.secrets.update', $secret['name']) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <textarea name="value" required rows="4"
                                                              placeholder="New secret value..."
                                                              class="w-full rounded-lg bg-gray-800 border border-gray-700 px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono mb-3"></textarea>
                                                    <div class="flex gap-2 justify-end">
                                                        <button type="button" @click="showUpdate = false"
                                                                class="bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm px-4 py-2 rounded-lg transition-colors">
                                                            Cancel
                                                        </button>
                                                        <button type="submit"
                                                                class="bg-orange-600 hover:bg-orange-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                                                            Update
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500 text-sm">No secrets found in this project.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-pulse-devops::layouts.app>
