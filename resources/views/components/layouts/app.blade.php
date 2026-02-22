<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Pulse DevOps' }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
    @livewireStyles
</head>
<body class="bg-gray-950 text-gray-200 antialiased min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('pulse') }}" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">
                    &larr; Pulse
                </a>
                <span class="text-gray-700">/</span>
                <h1 class="text-xl font-semibold text-gray-100">{{ $title ?? 'DevOps' }}</h1>
            </div>
            <nav class="flex items-center gap-3 text-sm text-gray-500">
                <a href="{{ route('pulse-devops.migrations') }}" class="hover:text-gray-300 transition-colors {{ request()->routeIs('pulse-devops.migrations') ? 'text-gray-200 font-medium' : '' }}">Migrations</a>
                <a href="{{ route('pulse-devops.database') }}" class="hover:text-gray-300 transition-colors {{ request()->routeIs('pulse-devops.database') ? 'text-gray-200 font-medium' : '' }}">Database</a>
                <a href="{{ route('pulse-devops.secrets') }}" class="hover:text-gray-300 transition-colors {{ request()->routeIs('pulse-devops.secrets') ? 'text-gray-200 font-medium' : '' }}">Secrets</a>
                <a href="{{ route('pulse-devops.cloud-run-jobs') }}" class="hover:text-gray-300 transition-colors {{ request()->routeIs('pulse-devops.cloud-run-jobs') ? 'text-gray-200 font-medium' : '' }}">Cloud Run Jobs</a>
            </nav>
        </div>

        @if(session('message'))
            <div class="mb-6 rounded-lg px-4 py-3 text-sm {{ session('status') === 'error' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-green-500/10 text-green-400 border border-green-500/20' }}">
                {{ session('message') }}
            </div>
        @endif

        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
