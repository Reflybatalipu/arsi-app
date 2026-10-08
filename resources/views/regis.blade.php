@extends('layouts.app')

@section('title', 'Registrasi')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rounded-lg bg-white p-6 shadow">
            <h1 class="mb-4 text-lg font-semibold">Registrasi Akun</h1>

            <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="mb-1 block text-sm">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label for="email" class="mb-1 block text-sm">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label for="role" class="mb-1 block text-sm">Peran</label>
                    <select name="role" id="role" required
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                        <option value="mahasiswa" @selected(old('role') === 'mahasiswa')>Mahasiswa</option>
                        <option value="ketua_tingkat" @selected(old('role') === 'ketua_tingkat')>Ketua Tingkat</option>
                    </select>
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1 block text-sm">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                </div>

                <button type="submit"
                        class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Daftar
                </button>
            </form>

            <p class="mt-4 text-center text-sm text-gray-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Masuk</a>
            </p>
        </div>
    </div>
@endsection
