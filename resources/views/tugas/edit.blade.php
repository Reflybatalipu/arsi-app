@extends('layouts.app')

@section('title', 'Ubah Tugas')

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="mb-4 text-xl font-semibold">Ubah Tugas</h1>

        <div class="rounded-lg bg-white p-6 shadow">
            <form method="POST" action="{{ route('tugas.update', $tugas) }}" class="space-y-4">
                @csrf
                @method('PUT')
                @include('tugas._form')

                <div class="flex gap-3">
                    <button type="submit"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        Perbarui
                    </button>
                    <a href="{{ route('tugas.show', $tugas) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
