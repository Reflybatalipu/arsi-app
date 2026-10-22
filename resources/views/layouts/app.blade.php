<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <title>@yield('title', 'Manajemen Tugas')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-mica">
    <nav class="sticky top-0 z-30 border-b border-stroke bg-surface/80 backdrop-blur-xl">
        <div class="mx-auto flex w-full max-w-6xl flex-wrap items-center justify-between gap-x-4 gap-y-2 px-4 py-3 sm:gap-x-6 sm:px-6 sm:py-4 lg:px-8">
            <div class="flex items-center gap-1 sm:gap-2">
                <a href="{{ route('dashboard') }}" class="mr-2 flex items-center gap-2 rounded-md px-1.5 py-1 font-semibold text-accent">
                    <x-fa name="clipboard-check" /> Manajemen Tugas
                </a>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded-md px-2.5 py-1.5 text-sm text-ink-2 transition hover:bg-hover">
                    <x-fa name="house" /> Beranda
                </a>
                <a href="{{ route('tugas.index') }}" class="flex items-center gap-2 rounded-md px-2.5 py-1.5 text-sm text-ink-2 transition hover:bg-hover">
                    <x-fa name="list-check" /> Tugas
                </a>
                <a href="{{ route('agenda.index') }}" class="flex items-center gap-2 rounded-md px-2.5 py-1.5 text-sm text-ink-2 transition hover:bg-hover">
                    <x-fa name="calendar-days" /> Agenda
                </a>
            </div>

            @auth
                <div class="flex items-center gap-3">
                    <span class="hidden items-center gap-2 text-sm sm:flex">
                        <x-fa name="circle-user" class="text-lg text-ink-4" />
                        {{ auth()->user()->name }}
                    </span>
                    <span class="rounded-md bg-accent-soft px-2 py-0.5 text-xs font-medium text-accent">
                        <x-fa name="shield-halved" class="mr-1" />{{ auth()->user()->role->label() }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            <x-fa name="arrow-right-from-bracket" /> Keluar
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </nav>

    <main class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-200">
                <x-fa name="circle-check" class="text-emerald-600 dark:text-emerald-400" />
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/60 dark:text-red-200">
                <p class="mb-1 flex items-center gap-2 font-medium">
                    <x-fa name="triangle-exclamation" class="text-red-600 dark:text-red-400" /> Ada yang perlu diperbaiki:
                </p>
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
