@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="card p-6">
            <div class="mb-5 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-md bg-accent-soft text-accent">
                    <x-fa name="right-to-bracket" />
                </span>
                <div>
                    <h1 class="text-lg font-semibold">Masuk</h1>
                    <p class="text-xs text-gray-500">Selamat datang kembali di Manajemen Tugas.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="label"><x-fa name="envelope" class="text-gray-400" />Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="input">
                </div>

                <div>
                    <label for="password" class="label"><x-fa name="lock" class="text-gray-400" />Password</label>
                    <input type="password" name="password" id="password" required
                           class="input">
                </div>

                <label class="flex cursor-pointer items-center gap-2 text-sm">
                    <input type="checkbox" name="remember" value="1" class="rounded border-gray-300">
                    <x-fa name="circle-check" class="text-gray-400" />Ingat saya
                </label>

                <button type="submit" class="btn btn-primary w-full">
                    <x-fa name="right-to-bracket" /> Masuk
                </button>
            </form>

            <p class="mt-4 border-t border-gray-100 pt-4 text-center text-sm text-gray-600">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-medium text-accent hover:underline">Daftar</a>
            </p>
        </div>
    </div>
@endsection
