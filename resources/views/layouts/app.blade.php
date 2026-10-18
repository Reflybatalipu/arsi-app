<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Manajemen Tugas')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 text-gray-900">
    <nav class="bg-white border-b border-gray-200">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-3">
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="font-semibold text-blue-600">Manajemen Tugas</a>
                <a href="{{ route('dashboard') }}" class="text-sm hover:text-blue-600">Beranda</a>
                <a href="{{ route('tugas.index') }}" class="text-sm hover:text-blue-600">Tugas</a>
                <a href="{{ route('agenda.index') }}" class="text-sm hover:text-blue-600">Agenda</a>
            </div>

            @auth
                <div class="flex items-center gap-3">
                    <span class="text-sm">{{ auth()->user()->name }}</span>
                    <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">
                        {{ auth()->user()->role->label() }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:underline">Keluar</button>
                    </form>
                </div>
            @endauth
        </div>
    </nav>

    <main class="mx-auto max-w-5xl px-4 py-6">
        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
