@extends('layouts.app')

@section('title', 'Registrasi')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="card p-6">
            <div class="mb-5 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-md bg-accent-soft text-accent">
                    <x-fa name="user-plus" />
                </span>
                <div>
                    <h1 class="text-lg font-semibold">Registrasi Akun</h1>
                    <p class="text-xs text-ink-3">Buat akun untuk mulai mengatur tugas dan agenda.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="label"><x-fa name="user" class="text-ink-4" />Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="input">
                </div>

                <div>
                    <label for="email" class="label"><x-fa name="envelope" class="text-ink-4" />Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="input">
                </div>

                <div>
                    <label for="role" class="label"><x-fa name="shield-halved" class="text-ink-4" />Peran</label>
                    <select name="role" id="role" required class="input cursor-pointer">
                        <option value="mahasiswa" @selected(old('role') === 'mahasiswa')>Mahasiswa</option>
                        <option value="ketua_tingkat" @selected(old('role') === 'ketua_tingkat')>Ketua Tingkat</option>
                    </select>
                </div>

                <div>
                    <label for="password" class="label"><x-fa name="lock" class="text-ink-4" />Password</label>
                    <input type="password" name="password" id="password" required
                           class="input">
                </div>

                <div>
                    <label for="password_confirmation" class="label"><x-fa name="lock" class="text-ink-4" />Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="input">
                </div>

                <button type="submit" class="btn btn-primary w-full">
                    <x-fa name="user-plus" /> Daftar
                </button>
            </form>

            <p class="mt-4 border-t border-line pt-4 text-center text-sm text-ink-2">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-medium text-accent hover:underline">Masuk</a>
            </p>
        </div>
    </div>
@endsection
