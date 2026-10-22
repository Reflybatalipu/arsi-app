@extends('layouts.app')

@section('title', 'Tambah Agenda')

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="mb-4 flex items-center gap-2 text-xl font-semibold"><x-fa name="plus" class="text-indigo-500" />Tambah Agenda</h1>

        <div class="card p-6">
            <form method="POST" action="{{ route('agenda.store') }}" class="space-y-4">
                @csrf
                @include('agenda._form')

                <div class="flex gap-3 border-t border-gray-100 pt-4">
                    <button type="submit" class="btn btn-primary">
                        <x-fa name="floppy-disk" /> Simpan
                    </button>
                    <a href="{{ route('agenda.index') }}" class="btn btn-secondary">
                        <x-fa name="xmark" /> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
