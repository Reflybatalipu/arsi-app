@extends('layouts.app')

@section('title', 'Ubah Tugas')

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="mb-4 flex items-center gap-2 text-xl font-semibold"><x-fa name="pen" class="text-accent" />Ubah Tugas</h1>

        <div class="card p-6">
            <form method="POST" action="{{ route('tugas.update', $tugas) }}" class="space-y-4">
                @csrf
                @method('PUT')
                @include('tugas._form')

                <div class="flex gap-3 border-t border-line pt-4">
                    <button type="submit" class="btn btn-primary">
                        <x-fa name="floppy-disk" /> Perbarui
                    </button>
                    <a href="{{ route('tugas.show', $tugas) }}" class="btn btn-secondary">
                        <x-fa name="xmark" /> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
